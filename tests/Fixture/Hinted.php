<?php
namespace Thunder\Hinter\Tests\Fixture;

use Thunder\Hinter\HinterTrait;

class Hinted
    {
    use HinterTrait;

    private $nativeProperty;

    public function nativeMethod($string, $integer, $float)
        {
        return null;
        }
    }