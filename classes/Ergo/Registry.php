<?php

/**
 * A locator that associates objects with string keys. Objects can either
 * be registered, or a factory can be provided and the object can be created
 * the first time it is requested.
 */
class Ergo_Registry
{
	private $_instances;
	private $_factories;

	/**
	 * @param string
	 * @return object
	 */
	function lookup($key)
	{
		if (!isset($this->_instances[$key]) &&
			isset($this->_factories[$key]))
		{
			$this->register($key, $this->_factories[$key]->create());
		}

		if(!isset($this->_instances[$key]))
		{
			throw new Ergo_RegistryException("No entry for key '$key'");
		}

		return $this->_instances[$key];
	}

	/**
	 * @param string $key
	 * @param object $object
	 */
	function register($key, $object)
	{
		$this->_instances[$key] = $object;
		return $this;
	}

	/**
	 * @param string $key
	 * @param Ergo_Factory $factory
	 */
	function factory($key, Ergo_Factory $factory)
	{
		$this->_factories[$key] = $factory;
		return $this;
	}

	/**
	 * @param string
	 * @return bool
	 */
	function isRegistered($key)
	{
		return isset($this->_instances[$key]) ||
			isset($this->_factories[$key]);
	}
}
