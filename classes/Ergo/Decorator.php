<?php

namespace Ergo;

/**
 * A generic decorator for forwarding method calls and property access to a delegate
 */
class Decorator
{
	private $__instance;

	/**
	 * Constructor.
	 * @param Object $object
	 */
	public function __construct($object)
	{
		$this->__setObject($object);
	}

	/**
	 * Returns the decorated object.
	 */
	public function __getObject()
	{
		return $this->__instance;
	}

	/**
	 * Replace the decorated delegate with the specified object.
	 */
	public function __setObject($object)
	{
		$this->__instance = $object;
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
		$callback = array($this->__instance, $method);

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
		$this->__instance->$name = $value;
	}

	/**
	 * Forward property get.
	 *
	 * @param String $name, property name
	 * @return Unknown
	 */
	public function __get($name)
	{
		return $this->__instance->$name;
	}
}
