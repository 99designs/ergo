<?php

namespace Ergo\Routing;

use Ergo\Http;

abstract class AbstractController implements Controller
{
	private $_router;
	private $_filterChain;

	public function __construct()
	{
		$this->_filterChain = new RequestFilterChain();
	}

	/**
	 * Adds an {@link Ergo\Routing\RequestFilter} to the controller
	 */
	public function addRequestFilter(RequestFilter $filter)
	{
		$this->_filterChain->addFilter($filter);
		return $this;
	}

	/**
	 * Filters a request through the internal filter chain
	 */
	public function filterRequest($request)
	{
		return $this->_filterChain->filter($request);
	}

	/**
	 * @return ResponseBuilder
	 */
	public function responseBuilder()
	{
		return new Http\ResponseBuilder();
	}
}

