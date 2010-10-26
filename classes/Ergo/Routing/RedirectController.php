<?php

namespace Ergo\Routing;

class RedirectController implements Controller
{
	private $_route;

	public function __construct($route)
	{
		$this->_route = $route;
	}

	/* (non-phpdoc)
	 * @see Controller::execute()
	 */
	public function execute($request)
	{
		$router = $request->getRouter();
		$builder = new \Ergo\Http\ResponseBuilder();

		// these parameters are interpolated into the redirected url
		$params = $request->getRouteMatch()->getParameters();

		// TODO: return a full RFC compliant url
		return $builder
			->found($router->buildUrl($this->_route, $params))
			->build()
			;
	}
}
