<?php

/**
 * Basic SPL class loader
 */
class Ergo_ClassLoader
{
	private $_paths = array();

	/**
	 * Registers this class as an SPL class loader.
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'loadClass'));
		return $this;
	}

	/**
	 * Unregisters this class as an SPL class loader, does not attempt to
	 * unregister include_path entries.
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));
		return $this;
	}

	/**
	 * Returns the class file for a particular class name
	 * @return string
	 */
	protected function classFile($className)
	{
		$classFile = preg_replace('#[_\\\]#', '/', $className).'.php';

		foreach ($this->_paths as $path)
		{
			$classPath = "$path/$classFile";
			if (file_exists($classPath))
			{
				return $classPath;
			}
		}
	}

	/**
	 * SPL autoload function, loads a class file based on the class name.
	 *
	 * @param string
	 */
	public function loadClass($className)
	{
		if (class_exists($className, false) || interface_exists($className, false))
		{
			return false;
		}

		if($classFile = $this->classFile($className))
		{
			require $classFile;
			return true;
		}

		return false;
	}

	/**
	 * Prepends one or more items to the include path of the class loader and
	 * the php include path.
	 * @param mixed $items Path or paths as string or array
	 */
	public function includePaths($path)
	{
		$paths = is_array($path) ? $path : array($path);
		$this->_paths = array_merge($paths,$this->_paths);
		return $this;
	}

	/**
	 * Exports the classloader path into the PHP system include path
	 */
	public function export()
	{
		$systemPaths = explode(PATH_SEPARATOR, get_include_path());
		set_include_path(implode(PATH_SEPARATOR,
			array_merge($systemPaths,$this->_paths)));
		return $this;
	}
}
