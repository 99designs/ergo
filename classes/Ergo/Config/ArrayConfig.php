<?php

/**
 * A simple array based configuration implementation
 */
class Ergo_Config_ArrayConfig implements Ergo_Config
{
	protected $_data=array();

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
}

