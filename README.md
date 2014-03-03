README
=====

[![Build Status](https://secure.travis-ci.org/thunderer/Hinter.png?branch=master)](http://travis-ci.org/thunderer/Hinter)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9b5c49fd-fa4d-4688-898a-8ba003475c54/mini.png)](https://insight.sensiolabs.com/projects/9b5c49fd-fa4d-4688-898a-8ba003475c54)

Introduction
-----------

[PHP](http://www.php.net/) does not support [method overloading](http://pl1.php.net/language.oop5.overloading), you can have only one function or method in given scope, regardless of the number or types of required arguments. It is also not a strongly typed language, but it introduced a mechanism called [Type Hinting](http://www.php.net/manual/en/language.oop5.typehinting.php) to help programmers enforce argument types on their own code structures.

ThunderHinter is a library that brings all of those features to the language at the cost of slightly more code to write as they need to be configured.

Requirements
-----------

ThunderHinter requires only PHP 5.4+.

Installation
-----------

You can install `ThunderHinter` with `Composer`:

```json
{
    "require": {
        "thunderer/hinter": "dev-master"
    }
}
```

Run `composer install` or `composer update` as required. 

Usage
----

Say you want to make a `Calculator` class with two basic operations: addition and subtraction. Let's assume that first attempt will look like the listing below:

```php
<?php
class Calculator
    {
    private $state;

    public function __construct() { $this->state = 0; }

    public function getState() { return $this->state; }
    
    public function plusOne($arg) { $this->state += $arg; }
    public function plusTwo($arg1, $arg2) { $this->state += ($arg1 + $arg2); }
    public function minusOne($arg) { $this->state -= $arg; }
    public function minusTwo($arg1, $arg2) { $this->state -= ($arg1 + $arg2); }
    }
```

First, you need to configure ThunderHinter instance. Using Calculator example above, relevant code looks as follows:

```php
<?php
use Thunder\Hinter\Hinter;

$config = array(
    'plus' => array(
        'plusOne' => array('numeric'),
        'plusTwo' => array('integer', 'double'),
        ),
    'minus' => array(
        'minusOne' => array('numeric'),
        'minusTwo' => array('integer', 'double'),
        ),
    );
$hinter = new Hinter($config);
```

Having such configuration, you've just configured two additional methods: `plus` and `minus` with two variants - with one and two arguments, the first restricted to "numeric" argument and second to integer as first and double as second argument.

Now you need to know that ThunderHinter can be used (mainly) in two ways:

* implicit, in which you execute it inside magic `__call()` method,
* explicit, in which you execute it manually.

To implement implicit flow, you need to embed instance inside Calculator class like the listing below:

```php
class Calculator
    {
    private $state;
    private $hinter;

    public function __construct()
        {
        $config = array(/* EXACTLY LIKE THAT ABOVE */);
        $this->hinter = new Hinter($config);
        $this->state = 0;
        }

    public function getState() { return $this->state; }
    
    private function plusOne($arg) { $this->state += $arg; }
    private function plusTwo($arg1, $arg2) { $this->state += ($arg1 + $arg2); }
    private function minusOne($arg) { $this->state -= $arg; }
    private function minusTwo($arg1, $arg2) { $this->state -= ($arg1 + $arg2); }

    public function __call($method, array $args)
        {
        return method_exists($this, $method)
            ? call_user_func_array(array($this, $method), $args)
            : $this->hinter->call($this, $method, $args);
        }
    }
```

To implement explicit flow, just move necessary code outside a class and call as you like:

```php
$config = array(/* EXACTLY LIKE THAT ABOVE */);
$hinter = new Hinter($config);
```

Now, when you execute something like:

```php
$calc = new Calculator();

$calc->plus(15); // plusOne()
$calc->plus(4, 5.4) // plusTwo()
$calc->minus(10); // minusOne()
$calc->minus(1, 6.7); // minusTwo()

$calc->multiply(4); // RuntimeException, method not configured
$calc->minus(new \stdClass(), 4) // RuntimeException, failed to match call
```

or in the explicit flow:

```php
$calc = new Calculator();

$hinter->call($calc, 'plus', array(15)); // plusOne()
$hinter->call($calc, 'plus', array(4, 5.4)); // plusTwo()
$hinter->call($calc, 'minus', array(10)); // minusOne()
$hinter->call($calc, 'minus', array(1, 6.7)); // minusTwo()

$hinter->call($calc, 'multiply', array(4)); // RuntimeException, method not configured
$hinter->call($calc, 'minus', array(new \stdClass(), 4)); // RuntimeException, failed to match call
```

ThunderHinter will get the method you tried to call, it will try to match the types of passed variables to one of the signatures stored inside configuration and return the value of the target function. If you try to do an invalid call, you'll get a nice `\RuntimeException` telling you what failed.

Configuration
--------------

This library supports all PHP types (these names are to be used in code):

* integer, float, double, numeric,
* string,
* bool,
* callable, Closure,
* null,
* object,
* class name (with namespaces or not) - simply enter it without preceding backslash, eg. Thunder\Hinter\Hinter.

If method accepts more than one argument, you need to supply as many array elements as required. If you want to accept multiple types on given argument, you need to pass array with those names in place of that argument. Configuration can be specified both in arrays and strings. If you use strings, separate type options with pipe `|` and arguments with comma `,`.

Examples
--------

* Method `sample` accepting one string parameter:

```php
$config = array(
    'sample' => array(
        'sample' => array('string'),
        ),
    );

$config = array(
    'sample' => array(
        'sample' => 'string',
        ),
    );
```

* Method `given` accepting two parameters, first as string and second as an integer:

```php
$config = array(
    'given' => array(
        'given' => array('string', 'integer'),
        ),
    );

$config = array(
    'given' => array(
        'given' => 'string,integer',
        ),
    );
```

* Method `other` accepting three parameters, first as string, second as a callable or string and third as a callable or Thunder\Hinter\Hinter instance:

```php
$config = array(
    'other' => array(
        'other' => array('string', array('string', 'callable'), array('callable', 'Thunder\Hinter\Hinter'))
        ),
    );

$config = array(
    'other' => array(
        'other' => 'string,string|callable,callable|Thunder\\Hinter\\Hinter',
        ),
    );
```