<?php
namespace Thunder\Hinter;

/**
 * Main library class expected to be injected into target objects and use hooks
 * in their __call() method.
 */
class ThunderHinter
    {
    /**
     * @var array
     */
    protected $calls;

    public function __construct(array $callsMetadata = array())
        {
        $this->calls = $callsMetadata;
        }

    /**
     * @return array
     */
    public function getCallsMetadata()
        {
        return $this->calls;
        }

    public function resolveVariableType($variable, $resolveObjectClassName = false)
        {
        $type = strtolower(gettype($variable));
        if('object' == $type && $resolveObjectClassName)
            {
            $type = get_class($variable);
            }
        return $type;
        }

    /* public function resolveArgumentTypes(array $arguments, $resolveObjectClassName = false)
        {
        $ret = array();
        foreach($arguments as $argument)
            {
            $ret[] = $this->resolveVariableType($argument, $resolveObjectClassName);
            }
        return $ret;
        } */

    public function isValidCall(array $args, array $callMetadata, $resolveObjectClassName = false)
        {
        $callMetadataCount = count($callMetadata);
        if(count($args) != $callMetadataCount)
            {
            return false;
            }
        for($i = 0; $i < $callMetadataCount; $i++)
            {
            if(!$this->isValidArgument($args[$i], $callMetadata[$i], $resolveObjectClassName))
                {
                return false;
                }
            }
        return true;
        }

    public function isValidArgument($argument, $expectedType, $resolveObjectClassName)
        {
        $argumentType = $this->resolveVariableType($argument, $resolveObjectClassName);
        return
            !(is_array($expectedType) && !in_array($argumentType, $expectedType)
            || (!is_array($expectedType) && $expectedType != $argumentType));
        }

    public function getMethodCallsMetadata($method)
        {
        if(isset($this->calls[$method]))
            {
            return $this->calls[$method];
            }
        throw new \LogicException(sprintf('There is no call metadata for method: "%s".', $method));
        }

    public function matchCall($method, $args)
        {
        $call = $this->getMethodCallsMetadata($method);
        foreach($call as $targetMethod => $callMetadata)
            {
            if($this->isValidCall($args, $callMetadata))
                {
                return $targetMethod;
                }
            }
        throw new \RuntimeException('There is no valid call within current scope!');
        }
    }