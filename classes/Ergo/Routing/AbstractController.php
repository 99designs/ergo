<?php

abstract class Ergo_Routing_AbstractController
	implements Ergo_Routing_Controller
{
	private $_routeMap;
	private $_filterChain;
	private $_responseBuilder;

	public function __construct()
	{
		$this->_filterChain = new Ergo_Routing_RequestFilterChain();
	}

	/**
	 * Adds an {@link Ergo_Routing_RequestFilter} to the controller
	 */
	public function addRequestFilter(Ergo_Routing_RequestFilter $filter)
	{
		$this->_filterChain->addFilter($filter);
		return $this;
	}

	/**
	 * Filters a request through the internal filter chain
	 */
	protected function filterRequest($request)
	{
		return $this->_filterChain->filter($request);
	}

	/**
	 * Sets a routemap used by the url generating functions
	 */
	public function setRouteMap($routeMap)
	{
		$this->_routeMap = $routeMap;
		return $this;
	}

	/**
	 * @return Ergo_Routing_RouteMap
	 */
	public function getRouteMap()
	{
		if (!isset($this->_routeMap))
		{
			throw new Ergo_Routing_Exception(
				"A routemap must be set into the controller first");
		}

		return $this->_routeMap;
	}

	/**
	 * Uses the RouteMap to build a URL for the given name and parameters.
	 * @param string $name
	 * @param array $parameters
	 */
	public function urlFor($name, $parameters = array())
	{
		return $this->getRouteMap()->buildUrl($name, $parameters);
	}

	/**
	 * @return Ergo_Http_ResponseBuilder
	 */
	public function responseBuilder()
	{
		return new Ergo_Http_ResponseBuilder();
	}
}

