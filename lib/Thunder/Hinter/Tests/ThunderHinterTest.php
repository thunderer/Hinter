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
    }