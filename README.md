# Hinter

[![Build Status](https://secure.travis-ci.org/thunderer/Hinter.png?branch=master)](http://travis-ci.org/thunderer/Hinter)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9b5c49fd-fa4d-4688-898a-8ba003475c54/mini.png)](https://insight.sensiolabs.com/projects/9b5c49fd-fa4d-4688-898a-8ba003475c54)

## Introduction

Hinter is an experiment which has two general objectives:

- provide some kind of a [polyfill](http://en.wikipedia.org/wiki/Polyfill) with features from latest PHP 5.x versions to older ones, notably before PHP 5.3 was released. This is not an easy task because even as PHP is a dynamic interpreted language there are limited possibilites of changing behavior of already loaded classes and created objects,
- enhance newest PHP versions with tools for more dynamic programming, enabling to for example dynamically change methods implementation in runtime.

Things currently implemented and allowed to play with:

- dynamic class properties (through `__set()` and `__get()` magic methods),
- custom type hinting rules for class methods (using `__call()` and `__callStatic` magic methods),
- dynamic class methods (through `__call()` and `__callStatic` magic methods).