<?php

namespace Ergo;

/**
 * A proxy for an item in the registry, looked up on each call
 */
class RegistryProxy
{
	private $_handle;

	/**
	 * Constructor
	 */
	public function __construct($handle)
	{
		$this->_handle = $handle;
	}

	/**
	 * Looks up the proxy object
	 */
	protected function __lookup()
	{
		return $this->_handle->get();
	}

	/**
	 * Forward method calls.
	 *
	 * @param String $method method name
	 * @param Array $args method arguments
	 * @return Unknown method return value
	*/
	public function __call($method, $args)
	{
		$callback = array($this->__lookup(), $method);

		if (is_callable($callback))
		{
			return call_user_func_array($callback, $args);
		}
		else
		{
			throw new \BadMethodCallException(sprintf(
				'%s::%s() is not callable',
				get_class($this->__instance),
				$method
			));
		}
	}

	/**
	 * Forward property set.
	 *
	 * @param String $name property name
	 * @param Unknown $value property value
	 */
	public function __set($name, $value)
	{
		$object = $this->__lookup();
		$object->$name = $value;
	}

	/**
	 * Forward property get.
	 *
	 * @param String $name, property name
	 * @return Unknown
	 */
	public function __get($name)
	{
		return $this->__lookup()->get()->$name;
	}
}

