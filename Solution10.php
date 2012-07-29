<?php

/**
 * Solution10 Library.
 *
 * This file literally just sets up the autoloader. All of the S10 library uses a PSR-0 compliant
 * naming convention, meaning if you already have a PSR-0 autoloader, you can ignore this file.
 *
 * @author 		Alex Gisby <alex@solution10.com>
 * @license 	MIT
 */

spl_autoload_register(function($className)
{
	$className = ltrim($className, '\\');
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strripos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if(file_exists($fileName))
	{
		require $fileName;
		return true;
	}
	
	return false;
});