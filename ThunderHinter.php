<?php
namespace Thunder\Hinter;

/**
 * Main library class expected to be injected into target objects and use hooks
 * in their __call() method.
 *
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
class ThunderHinter
    {
    /**
     * @var array Overloaded class method calls metadata
     */
    protected $calls;

    /**
     * Constructor
     *
     * @param array $callsMetadata Definition of overloaded class methods
     */
    public function __construct(array $callsMetadata = array())
        {
        $this->calls = $callsMetadata;
        }

    /**
     * Returns internal representation of registered overloaded calls metadata
     *
     * @return array
     */
    public function getCallsMetadata()
        {
        return $this->calls;
        }

    /**
     * Returns type of given $variable value. May resolve object class name if
     * second parameter equals true
     *
     * @param mixed $variable Value to process
     * @param bool $getClassName If type == 'object' decides whether return such value or its class name
     *
     * @see get_class()
     *
     * @return string Value type name
     */
    public function resolveVariableType($variable, $getClassName = false)
        {
        $type = strtolower(gettype($variable));
        if('object' == $type && $getClassName)
            {
            $type = get_class($variable);
            }
        return $type;
        }

    /**
     * Validates given argument set against passed call option metadata
     *
     * @param array $args Set of arguments
     * @param array $callMetadata Call option metadata
     * @param bool $getClassName Resolve objects class names?
     *
     * @return bool Whether arguments match call option or not
     */
    public function isValidCall(array $args, array $callMetadata, $getClassName = false)
        {
        $callMetadataCount = count($callMetadata);
        if(count($args) != $callMetadataCount)
            {
            return false;
            }
        for($i = 0; $i < $callMetadataCount; $i++)
            {
            if(!$this->isValidArgument($args[$i], $callMetadata[$i], $getClassName))
                {
                return false;
                }
            }
        return true;
        }

    /**
     * Validates single argument against expected type metadata
     *
     * @param mixed $argument Value to process
     * @param mixed $expectedType Expected type from call metadata
     * @param boolean $getClassName Resolve objects class names?
     *
     * @return bool Whether argument match expected type or not
     */
    public function isValidArgument($argument, $expectedType, $getClassName)
        {
        $argumentType = $this->resolveVariableType($argument, $getClassName);
        return
            !(is_array($expectedType) && !in_array($argumentType, $expectedType)
            || (!is_array($expectedType) && $expectedType != $argumentType));
        }

    /**
     * Returns all overloaded calls metadata for given method name
     *
     * @param string $method Method name
     *
     * @return array Overloaded method calls metadata
     *
     * @throws \LogicException When method calls metadata does not exist
     */
    public function getMethodCallsMetadata($method)
        {
        if(isset($this->calls[$method]))
            {
            return $this->calls[$method];
            }
        throw new \LogicException(sprintf('There is no call metadata for method: "%s".', $method));
        }

    /**
     * Determines matching object method from method name and specified arguments
     *
     * @param string $method Method name
     * @param array $args Call arguments
     *
     * @return string Target method name
     *
     * @throws \RuntimeException When there is no matching overloaded method call
     */
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