<?php

/**
 * A controller factory that uses a directory of controller files. Files should be named without
 * a Controller suffix. Note that underscores in controller names are translated to subdirectories
 */
class Ergo_Routing_ControllerDirectory implements Ergo_Routing_ControllerFactory
{
	function __construct($dir)
	{
		$this->_dir = rtrim($dir, '/');
	}

	/**
	 * @param $name
	 * @return Ergo_Routing_Controller
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

			/*
			if(!class_exists($className))
			{
				throw new Ergo_Exception("File $fileName doesn't contain $className");
			}
			*/
		}

		return new $className();
	}
}
