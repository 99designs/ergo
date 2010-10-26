<?php

namespace Ergo\Routing;

/**
 * A controller factory that uses a directory of controller files. Files should be named without
 * a Controller suffix. Note that underscores in controller names are translated to subdirectories
 */
class ControllerDirectory implements ControllerFactory
{
	function __construct($dir)
	{
		$this->_dir = rtrim($dir, '/');
	}

	/**
	 * @param $name
	 * @return Controller
	 */
	public function createController($name)
	{
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
	}
}
