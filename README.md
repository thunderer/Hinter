README
=====

[![Build Status](https://secure.travis-ci.org/thunderer/Hinter.png?branch=master)](http://travis-ci.org/thunderer/Hinter)

What is ThunderHinter?
----------------------------

ThunderHinter is a library that enables programmers to use custom type hinting rules apart from the features offered by PHP interpreter: http://php.net/manual/en/language.oop5.typehinting.php .

How to use it?
-----------------

If you want to apply your custom type hinting rules, you must initialize ThunderHinter object with a little bit of configuration. Let me show you an example that will probably answer all your questions:

```php
namespace Acme;

use Thunder\Hinter\ThunderHinter;

class Hinted
    {
    protected $sum = 0;

    protected function addOne($one)
        {
        $this->sum += $one;
        }

    protected function addTwo($one, $two)
        {
        $this->sum += $one + $two;
        }

    public function __call($name, $args)
        {
        $hinter = new ThunderHinter(array(
            'add' => array(
                'addOne' => array('integer'),
                'addTwo' => array('integer', 'integer'),
                ),
            ));
        return call_user_func_array(array($this, $hinter->matchCall($name, $args)), $args);
        }
    }

$hinted = new Hinted();
/* 1 */ $hinted->add(4);
/* 2 */ $hinted->add(5, 34);
/* 3 */ $hinted->add('string');
/* 4 */ $hinted->add(5, 34, 2);
```

In this code we created simple calculator class that keeps current value and allows adding one or two numbers at a time. We see that methods `addOne` and `addTwo` are `protected` (they can have any visibility you want, it is an example). Then we create the magic method `__call()` with definition of `add` method call that will call method `addOne` when there will be one argument of type `integer`, and `addTwo` when there will be two `integer`s (examples 1 and 2). Any invalid call in this context (invalid number of types or invalid argument type) will result in `\RuntimeException` being thrown at the end of matching process (examples 3 and 4).