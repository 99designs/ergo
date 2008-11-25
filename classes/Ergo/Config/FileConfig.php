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
	 */
	function loadFile($file, $optional=false)
	{
		if(!is_file($file) && !$optional)
		{
			throw new Ergo_Config_Exception("Failed to read $file");
		}

		$config = include($file);
		$this->_data = array_merge($this->_data,$config);
	}
}

