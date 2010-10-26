<?php

namespace Ergo\Routing;

/**
 * A match result from a lookup against a {@link RouteMap}
 */
class RouteMapMatch extends \ArrayIterator
{
	private $_name;
	private $_parameters;
	private $_tags;

	/**
	 * @param string $name
	 * @param array $parameters
	 * @params array $tags
	 */
	public function __construct($name, $parameters, $tags=null)
	{
		$this->_name = $name;
		$this->_parameters = $parameters;
		$this->_tags = $tags ? $tags : array();

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

	/**
	 * @return array
	 */
	public function getTags()
	{
		return $this->_tags;
	}

	/**
	 * @return array
	 */
	public function hasTag($tag)
	{
		return in_array($tag, $this->_tags);
	}
}
