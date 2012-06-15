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
    }