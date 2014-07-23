<?php
namespace Thunder\Hinter;

class Hinter
    {
    private static $config = array();

    public static function Init(array $config)
        {
        static::$config = $config;
        }

    public static function HasClass($class)
        {
        return array_key_exists($class, static::$config);
        }

    public static function HasClassMethod($class, $method)
        {
        return static::HasClass($class) && array_key_exists($method, static::$config[$class]['methods']);
        }

    public static function GetMethodCallMatch($class, $method, array $arguments)
        {
        $variants = static::$config[$class]['methods'][$method];
        foreach($variants as $variant)
            {
            if(static::IsValidCall($class, $variant, $arguments))
                {
                return $variant;
                }
            }

        return null;
        }

    public static function GetArgumentsSignature($args)
        {
        return implode(',', array_map(function($item) {
            return static::GetArgumentType($item);
            }, $args));
        }

    public static function ExtractState($object)
        {
        /**
         * @var $property \ReflectionProperty
         */
        $reflectionObject = new \ReflectionObject($object);
        $properties = $reflectionObject->getProperties();
        $return = array();
        foreach($properties as $property)
            {
            $property->setAccessible(true);
            $return[$property->getName()] = $property->getValue($object);
            }

        $managed = static::$config[get_class($object)]['properties'];
        $state = static::$config[get_class($object)]['state'];
        foreach($managed as $name => $data)
            {
            if(array_key_exists($name, $return))
                {
                continue;
                }
            $return[$name] = array_key_exists($name, $state)
                ? $state[$name]
                : null;
            }

        return $return;
        }

    public static function WriteState($object, array $state)
        {
        /**
         * @var $property \ReflectionProperty
         */
        $reflectionObject = new \ReflectionObject($object);
        foreach($state as $key => $value)
            {
            if(!$reflectionObject->hasProperty($key))
                {
                continue;
                }
            $property = $reflectionObject->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
            }

        $class = get_class($object);
        $managed = static::$config[$class]['properties'];
        foreach($managed as $name => $data)
            {
            static::SetProperty($object, $name, $state[$name]);
            }
        }

    public static function SetProperty($object, $name, $value)
        {
        static::$config[get_class($object)]['state'][$name] = $value;
        }

    public static function GetProperty($object, $name)
        {
        return static::$config[get_class($object)]['state'][$name];
        }

    private static function IsValidCall($class, $variant, array $args)
        {
        $metadata = static::$config[$class]['signatures'][$variant];
        $signature = $metadata['signature'];
        if(is_string($signature))
            {
            if('void' == $signature && empty($args))
                {
                return true;
                }
            }
        else if(is_array($signature))
            {
            $argumentsCount = count($args);
            $expectedArgumentsCount = count($signature);
            if($argumentsCount != $expectedArgumentsCount) { return false; }
            for($i = 0; $i < $expectedArgumentsCount; $i++)
                {
                if(!static::IsValidArgument($args[$i], $signature[$i]))
                    {
                    return false;
                    }
                }
            return true;
            }
        return false;
        }

    private static function IsValidArgument($value, $expected)
        {
        switch($expected)
            {
            case 'float': { return is_float($value); }
            case 'numeric': { return is_numeric($value); }
            case 'callable': { return is_callable($value); }
            }
        return is_array($expected)
            ? array_reduce($expected, function ($return, $item) use ($value) {
                return $return ?: static::IsValidArgument($value, $item);
                }, false)
            : static::GetArgumentType($value) === $expected;
        }

    private static function GetArgumentType($variable)
        {
        return is_object($variable)
            ? get_class($variable)
            : strtolower(gettype($variable));
        }
    }