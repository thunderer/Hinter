<?php
namespace Thunder\Hinter;

/**
 * Main library class expected to be injected into target objects and use hooks
 * in their __call() method.
 */
class ThunderHinter
    {
    protected $calls;

    public function __construct()
        {
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
    }