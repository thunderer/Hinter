<?php
namespace Thunder\Hinter\Tests;

use Symfony\Component\Yaml\Yaml;
use Thunder\Hinter\Container;
use Thunder\Hinter\Hinter;
use Thunder\Hinter\Tests\Fixture\Hinted;

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

        Hinter::Init(Yaml::parse(file_get_contents(__DIR__.'/Fixture/config/mapping.yaml')));

        $hinted = new Hinted(5.0);
        $this->assertEquals(5.0, $hinted->getState());

        $hinted->add(4.0);
        $this->assertEquals(9.0, $hinted->getState());
        }
    }