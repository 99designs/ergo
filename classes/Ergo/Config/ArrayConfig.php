<?php

/**
 * A simple array based configuration implementation
 */
class Ergo_Config_ArrayConfig implements Ergo_Config
{
	protected $_data=array();

	/**
	 * Constructor
	 */
	public function __construct($data=array())
	{
		$this->_data = $data;
	}

	/* (non-phpdoc)
	 * @see Ergo_Configuration::get
	 */
	public function get($key)
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
	public function exists($key)
	{
		return array_key_exists($key, $this->_data);
	}

	/* (non-phpdoc)
	 * @see Ergo_Configuration::getKeys
	 */
	public function keys()
	{
		return array_keys($this->_data);
	}
}

