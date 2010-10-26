<?php

namespace Ergo\Config;

/**
 * A configuration object that uses a file with a php array in it
 */
class FileConfig implements \Ergo\Config, \IteratorAggregate
{
	protected $_data=array();

	function __construct($files=array())
	{
		foreach((array)$files as $file) $this->loadFile($file);
	}

	/* (non-phpdoc)
	 * @see Ergo_Configuration::get
	 */
	function get($key)
	{
		if(!$this->exists($key))
		{
			throw new MissingKeyException("No config key '$key'");
		}

		return $this->_data[$key];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @chainable
	 */
	public function set($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}

	/* (non-phpdoc)
	 * @see Ergo_Configuration::exists
	 */
	function exists($key)
	{
		return array_key_exists($key, $this->_data);
	}

	/* (non-phpdoc)
	 * @see Ergo_Configuration::getKeys
	 */
	function keys()
	{
		return array_keys($this->_data);
	}

	/**
	 * Loads a config file and merges its contents with the previously loaded settings
	 * @param string the name of the file to load
	 * @param bool whether the file is optional
	 * @param mixed the name of the variable to save as the data
	 * @chainable
	 */
	function loadFile($file, $optional=false, $varname=false)
	{
		if(!is_file($file) && !$optional)
			throw new Exception("Failed to read config file '$file'");

		$newConfig = @include($file);

		if(!is_array($newConfig) && $varname)
			$newConfig = $$varname;

		if(!is_array($newConfig) && !$optional)
			throw new Exception("Config file '$file' doesn't contain a config");
		else if(is_array($newConfig))
			$this->_data = array_merge($this->_data, $newConfig);

		return $this;
	}

	/**
	 * Converts the config to an ArrayConfig, which is mutable
	 * @return ArrayConfig
	 */
	function toArrayConfig()
	{
		return new ArrayConfig($this->_data);
	}

	/* (non-phpdoc)
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->_data);
	}
}

