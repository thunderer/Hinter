<?php
namespace Thunder\Hinter;

class Container
    {
    private static $container = array();

    public static function Set($name, $value)
        {
        static::$container[$name] = $value;
        }

    public static function Get($name, $default = null)
        {
        if(null === $default && !static::Has($name))
            {
            throw new \RuntimeException(sprintf('Value [%s] does not exist!', $name));
            }
        return static::Has($name)
            ? static::$container[$name]
            : $default;
        }

    public static function Has($name)
        {
        return array_key_exists($name, static::$container);
        }
    }