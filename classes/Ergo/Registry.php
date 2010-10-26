<?php

namespace Ergo;

/**
 * A service locator object that maps keys to object instances. Can either
 * store actual instances, or lazily create objects when provided with a closure
 * or a {@link Factory} object.
 */
class Registry
{
	private $_registry=array();
	private $_triggers=array();

	/**
	 * Looks up a key in the registry, with an optional closure that that will
	 * be executed and the result stored if the key doesn't exist.
	 * @return Object
	 */
	public function lookup($key, $closure=null)
	{
		// if a closure was provided and we missed, store it
		if(!isset($this->_registry[$key]) && !is_null($closure))
		{
			$this->_registry[$key] = $this->_memoize($closure($key));
		}

		// the registry stores closures
		if(isset($this->_registry[$key]))
		{
			return $this->_registry[$key]->__invoke();
		}
		// try any registered triggers
		else if(isset($this->_triggers[$key]))
		{
			$this->_triggers[$key]->__invoke();
			unset($this->_triggers[$key]);
			return $this->lookup($key);
		}
		else
		{
			throw new RegistryException("No entry for key '$key'");
		}
	}

	/**
	 * Registers an object for later retrieval by key. Any existing key is overwritten.
	 * @chainable
	 */
	public function register($key, $object)
	{
		$this->_registry[$key] = $this->_memoize($object);
		return $this;
	}

	/**
	 * Registers a {@link Factory} or closure to be invoked on the first lookup
	 * @param string
	 * @param mixed Factory or Closure
	 */
	function factory($key, $factory)
	{
		if($factory instanceof Factory)
		{
			$this->_registry[$key] = function() use($factory) {
				return $factory->create();
			};
		}
		else if(is_callable($factory))
		{
			$this->_registry[$key] = $factory;
		}
		else
		{
			throw new RegistryException(
				"Parameter must be a Factory or Closure");
		}

		return $this;
	}

	/**
	 * Sets a closure that is called
	 * @param mixed either a string or an array of keys
	 * @param callback a php callback or an Ergo_Script object
	 * @chainable
	 */
	public function trigger($keys, $callable)
	{
		$keys = is_array($keys) ? $keys : array($keys);

		// add triggers for all arguments
		foreach($keys as $key)
		{
			$this->_triggers[$key] = $callable;
		}

		return $this;
	}

	/**
	 * Creates a handle to a particular registry key
	 */
	public function handle($key)
	{
		return new RegistryHandle($this, $key);
	}

	/**
	 * Returns a closure that returns an object.
	 * @return Closure
	 */
	private function _memoize($object)
	{
		return function() use($object) { return $object; };
	}

	/**
	 * Returns whether an object
	 */
	public function isRegistered($key)
	{
		return isset($this->_registry[$key]);
	}

	/**
	 * Magic property access
	 */
	public function __get($key)
	{
		return $this->lookup($key);
	}

	/**
	 * Magic property access for isset
	 */
	public function __isset($key)
	{
		return $this->isRegistered($key);
	}
}
