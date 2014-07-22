<?php
namespace Thunder\Hinter\Tests;

use Thunder\Hinter\Hinter;

class Hinted
    {
    private $state;
    private $hinter;

    public function __construct()
        {
        $config = array(
            'plus' => array(
                'plusOne' => array('numeric'),
                'plusTwo' => array('integer', 'double'),
                ),
            'minus' => array(
                'minusOne' => 'numeric',
                'minusTwo' => 'integer,double',
                ),
            'invalid' => array(
                'invalidCall' => 'null',
                ),
            'setState' => 'integer',
            );
        $this->hinter = new Hinter($config);
        $this->state = 0;
        }

    public function getState()
        {
        return $this->state;
        }
    
    private function plusOne($arg) { $this->state += $arg; }
    private function plusTwo($arg1, $arg2) { $this->state += ($arg1 + $arg2); }
    private function minusOne($arg) { $this->state -= $arg; }
    private function minusTwo($arg1, $arg2) { $this->state -= ($arg1 + $arg2); }
    public function setState($int) { $this->state = $int; }

    public function __call($method, array $args)
        {
        return method_exists($this, $method)
            ? call_user_func_array(array($this, $method), $args)
            : $this->hinter->call($this, $method, $args);
        }
    }

class HinterTest extends \PHPUnit_Framework_TestCase
    {
    public function testImplementation()
        {
        $hinted = new Hinted();
        $calls = array(
            array('plus', array(0), 0),
            array('plus', array(0, 1.5), 1.5),
            array('minus', array(0), 1.5),
            array('minus', array(0, 0.5), 1),
            array('setState', array(100), 100),
            );

        $this->assertEquals(0, $hinted->getState());
        foreach($calls as $call)
            {
            call_user_func_array(array($hinted, $call[0]), $call[1]);
            $this->assertEquals($call[2], $hinted->getState());
            }

        $this->setExpectedException('\RuntimeException');
        call_user_func_array(array($hinted, 'invalid'), array(null));
        }

    /**
     * @dataProvider provideMethods
     */
    public function testMethods($method, array $args, $target, $exception)
        {
        $config = array(
            'add' => array(
                'addOne' => array('integer'),
                'addTwo' => array('integer', 'integer'),
                'addThree' => array('integer', array('integer', 'double'), 'integer'),
                ),
            'sub' => array(
                'subOne' => 'integer',
                'subTwo' => 'integer,integer',
                'subThree' => 'integer|double,integer,integer',
                'subObject' => 'object,stdClass',
                ),
            'other' => array(
                'call' => array('callable'),
                'callOption' => array(array('numeric'), array('integer', 'Closure')),
                ),
            'hinted' => 'integer,string',
            );
        $hinter = new Hinter($config);
        if(null !== $exception)
            {
            $this->setExpectedException($exception);
            }
        $result = $hinter->match($method, $args);
        $this->assertEquals($target, $result);
        }

    public function provideMethods()
        {
        return array(
            array('add', array(2), 'addOne', null),
            array('add', array(2, 56), 'addTwo', null),
            array('add', array(2, new \stdClass()), null, '\RuntimeException'),
            array('add', array(4.45, false), null, 'exception'),
            array('add', array(4, 4.45, 4), 'addThree', null),
            array('add', array(4, 5, 4), 'addThree', null),
            array('add', array(4, false, 4), null, '\RuntimeException'),

            array('sub', array(4), 'subOne', null),
            array('sub', array(new \stdClass(), new \stdClass()), 'subObject', null),
            array('sub', array(1.5, 1, 1), 'subThree', null),

            array('other', array(function() {}), 'call', null),
            array('other', array(5.4, function() {}), 'callOption', null),

            array('invalid', array(), 'invalid', '\RuntimeException'),

            array('hinted', array(4, 'str'), 'hinted', null),
            );
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidMethodMetadata()
        {
        new Hinter(array('add' => null));
        }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidMethodCallMetadata()
        {
        new Hinter(array('add' => array('addOne' => null)));
        }
    }