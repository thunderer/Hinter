<?php
namespace Thunder\Hinter;

/**
 * ThunderHinter library main class
 *
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class Hinter
    {
    private $methods = array();

    /**
     * Yep, it's a constructor.
     *
     * @param array $methods Methods calls definitions
     *
     * @throws \RuntimeException When method definition is invalid
     */
    public function __construct(array $methods)
        {
        foreach($methods as $name => $calls)
            {
            if(!(is_array($calls) || is_string($calls)))
                {
                $message = 'Invalid %s method calls metadata: %s.';
                throw new \RuntimeException(sprintf($message, $name, json_encode($calls)));
                }
            $this->configureMethod($name, $calls);
            }
        }

    /**
     * Matches method call against given arguments array and returns target
     * method name to call on your object
     *
     * @param string $method Overloaded method name
     * @param array $args Arguments array
     *
     * @return mixed Target method return value
     *
     * @throws \RuntimeException When method or matching call was not found
     */
    public function match($method, array $args)
        {
        if(!array_key_exists($method, $this->methods))
            {
            $message = 'Method %s was not configured. Available: %s.';
            $methods = implode(',', array_keys($this->methods));
            $thrown = sprintf($message, $method, $methods);
            throw new \RuntimeException($thrown);
            }
        $call = $this->methods[$method];
        foreach($call as $targetMethod => $callMetadata)
            {
            if($this->isValidCall($args, $callMetadata))
                {
                return $targetMethod;
                }
            }

        $message = 'Failed to match call %s!';
        $signature = $this->getSignature($this, $method, $args);
        throw new \RuntimeException(sprintf($message, $signature));
        }

    /**
     * Shortcut method for implementations to use for example inside __call()
     * method: finds target method using match(), calls it on passed object
     * and returns its value
     *
     * @param object $object Instance to call methods on
     * @param string $method Overloaded method name
     * @param array $args Arguments array
     *
     * @return mixed Whatever target method returns
     *
     * @throws \RuntimeException If instance does not implement target method
     */
    public function call($object, $method, $args)
        {
        $target = $this->match($method, $args);
        if(!method_exists($object, $target))
            {
            $message = 'Matched method %s but it does not exist!';
            $signature = $this->getSignature($this, $target, $args);
            throw new \RuntimeException(sprintf($message, $signature));
            }

        return call_user_func_array(array($object, $target), $args);
        }

    private function configureMethod($name, $calls)
        {
        $this->methods[$name] = array();
        if(is_string($calls))
            {
            $calls = array($name => $calls);
            }
        foreach($calls as $target => $call)
            {
            if(is_array($call))
                {
                $this->methods[$name][$target] = $call;
                }
            else if(is_string($call))
                {
                $parts = explode(',', $call);
                foreach($parts as $key => $part)
                    {
                    $parts[$key] = (false !== strpos($part, '|'))
                        ? explode('|', $part)
                        : $part;
                    }
                $this->methods[$name][$target] = $parts;
                }
            else
                {
                $message = 'Invalid %s method call definition.';
                throw new \RuntimeException(sprintf($message, $name));
                }
            }
        }

    private function isValidCall(array $args, array $callMetadata)
        {
        $expectedArgsCount = count($callMetadata);
        $argsCount = count($args);
        if($argsCount != $expectedArgsCount)
            {
            return false;
            }
        for($i = 0; $i < $expectedArgsCount; $i++)
            {
            if(!$this->isValidArgument($args[$i], $callMetadata[$i]))
                {
                return false;
                }
            }

        return true;
        }

    private function isValidArgument($value, $expected)
        {
        switch($expected)
            {
            // these types are not returned by gettype()
            case 'numeric': { return is_numeric($value); }
            case 'callable': { return is_callable($value); }
            case 'object': { return is_object($value); }
            case 'float': { return is_float($value); }
            }

        return is_array($expected)
            ? array_reduce($expected, function($return, $item) use($value) {
                return $return ?: $this->isValidArgument($value, $item);
                }, false)
            : $this->getArgumentType($value) === $expected;
        }

    private function getSignature($object, $method, array $args)
        {
        $types = array_map(function($item) {
            return $this->getArgumentType($item, true);
            }, $args);

        return get_class($object).'::'.$method.'('.implode(',', $types).')';
        }

    private function getArgumentType($variable)
        {
        return is_object($variable)
            ? get_class($variable)
            : strtolower(gettype($variable));
        }
    }