<?php
namespace Thunder\Hinter\Tests\Fixtures;

use Thunder\Hinter\ThunderHinter;

class SampleClass
    {
    protected $sum;

    /**
     * @var ThunderHinter
     */
    protected $hinter;

    public function __construct()
        {
        $this->sum = 0;
        $this->hinter = new ThunderHinter(array(
            'add' => array(
                'addOne' => array('integer'),
                'addTwo' => array('integer', 'integer'),
                ),
            ));
        }

    public function getSum()
        {
        return $this->sum;
        }

    /**
     * @return ThunderHinter
     */
    public function getHinter()
        {
        return $this->hinter;
        }

    /**
     * Add one value to the sum
     *
     * @param integer $one The value
     */
    public function addOne($one)
        {
        $this->sum += $one;
        }

    /**
     * Add two values to the sum
     *
     * @param integer $one The first value
     * @param integer $two The second value
     */
    public function addTwo($one, $two)
        {
        $this->sum += $one + $two;
        }

    public function __call($name, $args)
        {
        $method = $this->hinter->matchCall($args);
        return call_user_func_array(array($this, $method), $args);
        }
    }