<?php
namespace Thunder\Hinter\Tests\Fixtures;

use Thunder\Hinter\ThunderHinter;

class InheritanceClass extends ThunderHinter
    {
    protected $sum;

    protected $calls = array(
        'add' => array(
            'addOne' => array('integer'),
            'addTwo' => array('integer', 'integer'),
            ),
        );

    public function __construct()
        {
        $this->sum = 0;
        }

    public function getSum()
        {
        return $this->sum;
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

//    public function __call($name, $args)
//        {
//        return parent::inheritanceCall($name, $args, $this);
//        }

    public function sampleMethodOne(\PDO $connection, array $args, ThunderHinter $hinter)
        {
        }
    }