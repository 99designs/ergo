<?php

/**
 * A match result from a lookup against a {@link Ergo_Routing_RouteMap}
 */
class Ergo_Routing_RouteMapMatch
	extends ArrayIterator
{

	private $_name;
	private $_parameters;

	/**
	 * @param string $name
	 * @param array $parameters
	 */
	public function __construct($name, $parameters)
	{
		$this->_name = $name;
		$this->_parameters = $parameters;
		parent::__construct($parameters);
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
	 * @return string
	 */
	public function parameter($key,$default=false)
	{
		return isset($this[$key]) ? $this[$key] : $default;
	}
}
