<?php

namespace Ergo\Config;

/**
 * A simple array based configuration implementation
 */
class ArrayConfig implements \Ergo\Config, \IteratorAggregate
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

	/* (non-phpdoc)
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->_data);
	}
}

