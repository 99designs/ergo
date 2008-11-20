<?php

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
		$fileName = "$this->_dir/$className.php";

		if(!is_file($fileName) && !class_exists($className))
		{
			throw new Ergo_Exception("Missing controller file $fileName");
		}
		if(!class_exists($className))
		{
			require($fileName);
		}

		return new $className();
	}
}
