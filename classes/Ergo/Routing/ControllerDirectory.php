<?php

namespace Ergo\Routing;

/**
 * A controller factory that uses a directory of controller files.
 */
class ControllerDirectory implements ControllerResolver
{
	public function __construct($dir)
	{
		$this->_dir = rtrim($dir, '/');
	}

	/* (non-phpdoc)
	 * @see ControllerResolver::resolve()
	 */
	public function resolve($name)
	{
		/*
		$className = sprintf('%sController',$name);
		$fileName = str_replace('_','/',"$this->_dir/$className.php");

		if(!is_file($fileName) && !class_exists($className, false))
		{
			throw new Ergo_Exception("Missing controller file $fileName");
		}

		if(!class_exists($className, false))
		{
			require($fileName);
		}

		return new $className();
		*/
	}
}
