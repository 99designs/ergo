<?php

/**
 * A configuration object that uses a file with a php array in it
 */
class Ergo_Config_FileConfig implements Ergo_Config
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
			throw new Ergo_Config_MissingKeyException("No config key '$key'");
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
		{
			throw new Ergo_Config_Exception("Failed to read $file");
		}

		$returned = include($file);
		$config = $varname ? $$varname : $returned;
		$this->_data = array_merge($this->_data,$config);

		return $this;
	}

	/**
	 * Converts the config to an Ergo_Config_ArrayConfig, which is mutable
	 * @return Ergo_Config_ArrayConfig
	 */
	function toArrayConfig()
	{
		return new Ergo_Config_ArrayConfig($this->_data);
	}
}

