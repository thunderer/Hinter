<?php
function customAutoloader($className)
	{
	$className = str_replace('\\', '/', $className);
	$baseDir = dirname(__FILE__).'/';
	$paths = array(
		'lib/'
		);
	foreach($paths as $path)
		{
		$filePath = $baseDir.$path.$className.'.php';
		// echo $filePath."\n";
		if(true == file_exists($filePath))
			{
			require($filePath);
			}
		}
	// die($className);
	}

spl_autoload_register('\customAutoloader');