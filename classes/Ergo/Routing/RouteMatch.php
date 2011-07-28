<?php

namespace Ergo\Routing;

/**
 * A match result from a lookup against a {@link Router}
 */
class RouteMatch implements \IteratorAggregate
{
	private $_name;
	private $_parameters;
	private $_metadata;

	/**
	 * @param string $name
	 * @param array $parameters
	 * @params array $tags
	 */
	public function __construct($name, $parameters, $metadata=array())
	{
		$this->_name = $name;
		$this->_parameters = $parameters;
		$this->_metadata = $metadata;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->_parameters;
	}

	/**
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->_metadata;
	}

	/**
	 * @return bool
	 */
	public function hasMetadata($key)
	{
		return in_array($key, $this->_metadata);
	}

	/**
	 * @return string
	 */
	public function parameter($key,$default=false)
	{
		return isset($this[$key]) ? $this[$key] : $default;
	}

	/*
	 * @see IteratorAggregate
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->_parameters);
	}

	/**
	 * Magic getter method
	 */
	public function __get($key)
	{
		return $this->_parameters[$key];
	}
}
