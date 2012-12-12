<?php
require_once('vendor/autoload.php');

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();

$console
    ->register('generate:call')
    ->setDefinition(array(
        new InputArgument('path', InputArgument::REQUIRED, 'Path to class file'),
        ))
    ->setDescription('Greet someone.')
    ->setHelp('Generate __call() method body for specified class.')
    ->setCode(function(InputInterface $input, OutputInterface $output) {
        $path = $input->getArgument('path', '');
        $output->writeln(sprintf('Using path: %s:%s', __DIR__, $path));
        $filePath = __DIR__.DIRECTORY_SEPARATOR.$path;
        if(!file_exists($filePath))
            {
            $output->writeln(sprintf('File does not exist!'));
            return;
            }
        $code = file_get_contents($filePath);
        require_once($filePath);
        preg_match('/class\s([a-zA-Z0-9\\\\]+)/', $code, $matches);
        $className = isset($matches[1]) ? $matches[1] : '';
        preg_match('/namespace\s([a-zA-Z0-9\\\\]+)/', $code, $matches);
        $namespace = isset($matches[1]) ? $matches[1] : '';
        if(!$className || !$namespace)
            {
            $output->writeln(sprintf('Class name (%s) or namespace (%s) not found!', $className, $namespace));
            return;
            }
        $output->writeln(sprintf('Class name: %s:%s', $namespace, $className));
        $fqcn = $namespace.'\\'.$className;
        $object = new $fqcn();
        $reflectionObject = new \ReflectionObject($object);
        echo "\n";
        echo '/* '.str_pad('', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo '/* '.str_pad(' BEGIN THUNDERHINTER CODE ', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo '/* '.str_pad('', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo "\n";
        echo '$this->hinter = new ThunderHinter(array('."\n";
        foreach($reflectionObject->getMethods() as $reflectionMethod)
            {
            $parameters = $reflectionMethod->getParameters();
            if(!$parameters || preg_match('/^__/', $reflectionMethod->getName()))
                {
                continue;
                }
            echo sprintf('    \'%s\' => array('."\n", $reflectionMethod->getName());
            echo '        ';
            foreach($parameters as $reflectionParameter)
                {
                $class = $reflectionParameter->getClass();
                echo sprintf('\'%s%s\', ', $reflectionParameter->getName(), ($class ? ' ('.$class->getName().')' : ''));
                }
            echo "\n".'        ),'."\n";
            }
        echo '    ));'."\n";
        echo "\n";
        echo '/* '.str_pad('', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo '/* '.str_pad(' END THUNDERHINTER CODE ', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo '/* '.str_pad('', 74, '*', STR_PAD_BOTH).' */'."\n";
        echo "\n";
        });

$console->run();