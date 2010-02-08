<?php

/**
 * A handle to a specific registry key
 */
class Ergo_RegistryHandle
{
	private $_registry, $_key;

	/**
	 * Constructor
	 */
	public function __construct($registry, $key)
	{
		$this->_registry = $registry;
		$this->_key = $key;
	}

	/**
	 * @returns bool
	 */
	public function exists()
	{
		return $this->_registry->isRegistered($this->_key);
	}

	/**
	 * @returns mixed the value passed in
	 */
	public function set($value)
	{
		$this->_registry->register($this->_key, $value);
		return $value;
	}

	/**
	 * @returns mixed the value for a key, or the provided default
	 */
	public function get($default=null)
	{
		if(!$this->_registry->isRegistered($this->_key) && isset($default))
		{
			return $this->set($default);
		}
		else
		{
			return $this->_registry->lookup($this->_key);
		}
	}

	/**
	 * Returns a proxy object to the object refered to by the handle
	 * @return Ergo_RegistryProxy
	 */
	public function proxy()
	{
		return new Ergo_RegistryProxy($this);
	}
}

