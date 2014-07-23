<?php
namespace Thunder\Hinter\Tests;

use Symfony\Component\Yaml\Yaml;
use Thunder\Hinter\Container;
use Thunder\Hinter\Hinter;
use Thunder\Hinter\HinterTrait;

class Hinted
    {
    use HinterTrait;

    private $nativeProperty = 'valid';

    public function nativeMethod($string, $integer, $float)
        {
        return 'valid';
        }
    }

class HinterTest extends \PHPUnit_Framework_TestCase
    {
    public function testHinter()
        {
        Container::Set('setState', function(array &$state, array $args) { $state['state'] = $args[0]; });
        Container::Set('getState', function(array &$state, array $args) { return $state['state']; });
        Container::Set('addOne', function(array &$state, array $args) { $state['state'] += $args[0]; });
        Container::Set('addTwo', function(array &$state, array $args) { $state['state'] += ($args[0] + $args[1]); });
        Container::Set('subOne', function(array &$state, array $args) { $state['state'] -= $args[0]; });
        Container::Set('subTwo', function(array &$state, array $args) { $state['state'] -= ($args[0] + $args[1]); });

        Hinter::Init(Yaml::parse(file_get_contents(__DIR__.'/fixture/mapping.yaml')));

        $hinted = new Hinted(5.0);
        $this->assertSame(5.0, $hinted->getState());

        $hinted->add(4.0);
        $this->assertSame(9.0, $hinted->getState());

        $hinted->state = 1.0;
        $this->assertSame(1.0, $hinted->state);

        $this->assertSame('valid', $hinted->nativeMethod('', 0, 0.0));
        $this->assertSame('valid', $hinted->nativeProperty);
        }
    }