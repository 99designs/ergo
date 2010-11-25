<?php

namespace Ergo\Routing;

/**
 * A controller factory that uses a directory of controller files.
 */
class ControllerDirectory implements ControllerResolver
{
	private $_iterator;
	private $_callback;

	/**
	 * @param mixed either a directory path, or an iterator
	 * @param callback returns an instance of a controller, given file and controller name
	 */
	public function __construct($directoryIterator, $callback=null)
	{
		if(is_string($directoryIterator))
			$directoryIterator = new \DirectoryIterator($directoryIterator);

		$this->_iterator = $directoryIterator;
		$this->_callback = $callback ?: function($file, $className) {
			require_once($file);
			return new $className();
		};
	}

	/* (non-phpdoc)
	 * @see ControllerResolver::resolve()
	 */
	public function resolve($name)
	{
		foreach($this->_iterator as $file)
			if($file->getFilename() == "$name.php")
				return call_user_func($this->_callback, (string) $file, $name);

		throw new \Ergo\Exception("Unable to find a file for controller $name");
	}
}
