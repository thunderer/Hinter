<?php
namespace Thunder\Hinter\Tests;

use Thunder\Hinter\ThunderHinter;

class ThunderHinterTest extends \PHPUnit_Framework_TestCase
    {
    /**
     * @var ThunderHinter
     */
    protected $instance;

    public function setUp()
        {
        $this->instance = new ThunderHinter();
        }

    public function tearDown()
        {
        $this->instance = null;
        }

    public function testValidInstanceClass()
        {
        $this->assertInstanceOf('Thunder\Hinter\ThunderHinter', $this->instance);
        }

    public function resolveVariableTypeDataProvider()
        {
        return array(
            array(24, false, 'integer'),
            array(3.53, false, 'double'),
            array(new \stdClass(), false, 'object'),
            array(new \stdClass(), true, 'stdClass'),
            array(array(), true, 'array'),
            array(true, true, 'boolean'),
            array(null, true, 'null'),
            array('lorem ipsum', true, 'string'),
            array(new ThunderHinter(), true, 'Thunder\\Hinter\\ThunderHinter'),
            );
        }

    /**
     * @dataProvider resolveVariableTypeDataProvider
     */
    public function testResolveVariableType($value, $resolveObjectClassName, $expected)
        {
        $this->assertEquals($expected, $this->instance->resolveVariableType($value, $resolveObjectClassName));
        }

    public function isValidCallDataProvider()
        {
        return array(
            array(array(2, false), array('integer', 'boolean'), true, true),
            array(array(2, 2.54), array('integer', 'object'), true, false),
            array(array(2, new \stdClass()), array('integer', 'object'), false, true),
            );
        }

    /**
     * @dataProvider isValidCallDataProvider
     */
    public function testIsValidCall($args, $metadata, $resolveObjectClassName, $expected)
        {
        $this->assertEquals($expected, $this->instance->isValidCall($args, $metadata));
        }

    public function matchCallDataProvider()
        {
        return array(
            array(
                'config' => array(
                    'add' => array(
                        'addOne' => array('integer'),
                        'addTwo' => array('integer', 'integer'),
                        ),
                    ),
                'calls' => array(
                    array(array(2), 'addOne'),
                    array(array(2, 56), 'addTwo'),
                    array(array(2, new \stdClass()), 'exception'),
                    array(array(4.45, false), 'exception')
                    ),
                ),
            );
        }

    /**
     * @dataProvider matchCallDataProvider
     */
    public function testMatchCall(array $config, array $calls)
        {
        $instance = new ThunderHinter($config);
        foreach($calls as $call)
            {
            if('exception' == $call[1])
                {
                $this->setExpectedException('\RuntimeException');
                }
            $result = $instance->matchCall($call[0]);
            if('exception' != $call[1])
                {
                $this->assertEquals($call[1], $result);
                }
            }
        }

    public function testGetCallsMetadata()
        {
        $this->assertEquals(array(), $this->instance->getCallsMetadata());
        }
    }