<?php
spl_autoload_register(function($class)
    {
    $baseNamespace = 'Thunder\Hinter';
    if(0 === strpos(ltrim($class, '/'), $baseNamespace))
        {
        if(file_exists($file = __DIR__.'/../'.substr(str_replace('\\', '/', $class), strlen($baseNamespace)).'.php'))
            {
            require_once($file);
            }
        }
    });

if(file_exists($loader = __DIR__.'/../vendor/autoload.php'))
    {
    require_once $loader;
    }