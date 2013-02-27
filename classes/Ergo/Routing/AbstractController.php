<?php

namespace Ergo\Routing;

use Ergo\Http;

abstract class AbstractController implements Controller
{
	private $_router;

	public function __construct()
	{
	}

	public function setRouter($router)
	{
		$this->_router = $router;
	}

	public function urlFor($name, $parameters = array())
	{
		return $this->_router->buildUrl($name, $parameters);
	}

	/**
	 * @return ResponseBuilder
	 */
	public function responseBuilder()
	{
		return new Http\ResponseBuilder();
	}
}

