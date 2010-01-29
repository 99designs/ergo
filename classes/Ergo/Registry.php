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
	private $_triggers;

	/**
	 * @param string
	 * @return object
	 */
	function lookup($key)
	{
		// return fast if the instance exists
		if (isset($this->_instances[$key]))
		{
			return $this->_instances[$key];
		}
		// otherwise check factories
		else if(isset($this->_factories[$key]))
		{
			$this->register($key, $this->_factories[$key]->create());
		}

		// if it still doesn't exist, check triggers
		if(!isset($this->_instances[$key]) && isset($this->_triggers[$key]))
		{
			call_user_func($this->_triggers[$key]);

			if(!isset($this->_instances[$key]) && isset($this->_factories[$key]))
			{
				$this->register($key, $this->_factories[$key]->create());
			}
		}

		// try again, if not, give up
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
	 * Creates a handle to a particular registry key
	 */
	public function handle($key)
	{
		return new Ergo_RegistryHandle($this, $key);
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function isRegistered($key)
	{
		return isset($this->_instances[$key]) ||
			isset($this->_factories[$key]);
	}

	/**
	 * Sets a callback to be called when a lookup fails
	 * @param mixed either a string or an array of keys
	 * @param callback a php callback or an Ergo_Script object
	 * @chainable
	 */
	public function trigger($keys, $callable)
	{
		$keys = is_array($keys) ? $keys : array($keys);

		// convert objects with an execute method into php callback
		if(is_object($callable) && method_exists($callable,'execute'))
		{
			$callable = array($callable,'execute');
		}

		// add triggers for all arguments
		foreach($keys as $key)
		{
			$this->_triggers[$key] = $callable;
		}

		return $this;
	}
}
