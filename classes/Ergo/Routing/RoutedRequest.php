<?php

namespace Ergo\Routing;

/**
* A request sent via a {@link RoutedController}
 */
class RoutedRequest
{
	private $_delegate;
	private $_routeMatch;
	private $_routeMap;

	/**
	 * @param Ergo_Http_Request
	 * @param RouteMatch
	 * @param RouteMap
	 */
	public function __construct($request, $routeMatch, $routeMap)
	{
		$this->_delegate = $request;
		$this->_routeMatch = $routeMatch;
		$this->_routeMap = $routeMap;
	}

	/**
	 * @return RouteMap
	 */
	public function getRouteMap()
	{
		return $this->_routeMap;
	}

	/**
	 * @return RouteMatch
	 */
	public function getRouteMatch()
	{
		return $this->_routeMatch;
	}

	/**
	 * Proxy method calls to delegate HttpRequest.
	 */
	public function __call($method, $parameters)
	{
		if(!method_exists($this->_delegate,$method))
			throw new \BadMethodCallException("Request has no $method() method");

		return call_user_func_array(
			array($this->_delegate, $method),
			$parameters
		);
	}

}
