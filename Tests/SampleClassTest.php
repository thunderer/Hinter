<?php
namespace Thunder\Hinter\Tests;

use Thunder\Hinter\Tests\Fixtures\SampleClass;

class SampleClassTest extends \PHPUnit_Framework_TestCase
    {
    /**
     * @var SampleClass
     */
    protected $instance;

    public function setUp()
        {
        $this->instance = new SampleClass();
        }

    public function tearDown()
        {
        $this->instance = null;
        }

    public function testValidInstanceClass()
        {
        $this->assertInstanceOf('Thunder\Hinter\Tests\Fixtures\SampleClass', $this->instance);
        }

    public function testGetHinter()
        {
        $this->assertInstanceOf('Thunder\Hinter\ThunderHinter', $this->instance->getHinter());
        }

    public function testAddOne()
        {
        $this->instance->addOne(4);
        $this->assertEquals(4, $this->instance->getSum());
        }

    public function testAddTwo()
        {
        $this->instance->addTwo(4, 5);
        $this->assertEquals(9, $this->instance->getSum());
        }

    public function addDataProvider()
        {
        return array(
            array(array(2), 2),
            array(array(2, 6), 8),
            array(array(2, new \stdClass()), 'exception'),
            );
        }

    /**
     * @dataProvider addDataProvider
     */
    public function testAdd($args, $expected)
        {
        if('exception' == $expected)
            {
            $this->setExpectedException('\RuntimeException');
            }
        call_user_func_array(array($this->instance, 'add'), $args);
        if('exception' != $expected)
            {
            $this->assertEquals($expected, $this->instance->getSum());
            }
        }
    }