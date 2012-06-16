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

    public function resolveArgumentTypes(array $arguments, $resolveObjectClassName = false)
        {
        $ret = array();
        foreach($arguments as $argument)
            {
            $ret[] = $this->resolveVariableType($argument, $resolveObjectClassName);
            }
        return $ret;
        }

    public function isValidCall(array $args, array $callMetadata, $resolveObjectClassName = false)
        {
        if(count($args) != count($callMetadata))
            {
            return false;
            }
        return !count(array_diff_assoc($callMetadata, $this->resolveArgumentTypes($args, $resolveObjectClassName)));
        }

    public function matchCall($args)
        {
        foreach($this->calls as $call)
            {
            foreach($call as $targetMethod => $callMetadata)
                {
                if($this->isValidCall($args, $callMetadata))
                    {
                    return $targetMethod;
                    }
                }
            }
        throw new \RuntimeException('There is no valid call within current scope!');
        }
    }