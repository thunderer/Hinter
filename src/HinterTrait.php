<?php
namespace Thunder\Hinter;

trait HinterTrait
    {
    public function __construct()
        {
        return $this->__call(__FUNCTION__, func_get_args());
        }

    public function __set($property, $value)
        {
        if(property_exists(get_class($this), $property))
            {
            $this->{$property} = $value;
            }
        else
            {
            Hinter::SetProperty($this, $property, $value);
            }
        }

    public function __get($property)
        {
        if(property_exists(get_class($this), $property))
            {
            return $this->{$property};
            }
        return Hinter::GetProperty($this, $property);
        }

    public function __call($method, array $args)
        {
        var_dump($method);
        $class = get_class($this);
        if(!Hinter::HasClassMethod($class, $method))
            {
            $msg = 'Method %s was not configured inside class %s!';
            throw new \RuntimeException(sprintf($msg, $method, $class));
            }
        $target = Hinter::GetMethodCallMatch($class, $method, $args);
        if(null === $target)
            {
            $msg = 'Failed to match call to method %s with arguments %s!';
            throw new \RuntimeException(sprintf($msg, $method, Hinter::GetArgumentsSignature($args)));
            }
        if('@' == $target[0])
            {
            $callable = Container::Get(substr($target, 1));
            if(!is_callable($callable))
                {
                throw new \RuntimeException(sprintf('Callable %s is not callable!', $target));
                }
            $state = Hinter::ExtractState($this);
            $value = $callable($state, $args);
            Hinter::WriteState($this, $state);
            return $value;
            }
        else
            {
            return call_user_func_array(array($this, $target), $args);
            }
        }
    }