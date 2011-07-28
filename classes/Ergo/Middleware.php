<?php

namespace Ergo;

/**
 * A middleware for altering the request and response
 * @see Ergo\Application::middleware()
 */
class Middleware
{
	protected $application;
	protected $controller;

	/**
	 * Constructor
	 */
	public function __construct($controller, $application)
	{
		$this->application = $application;
		$this->controller = $controller;
	}

	/**
	 * Helper to modify the response via a callback
	 */
	protected function filterResponse($request, $type="text/*", $callback)
	{
		$response = $this->controller->execute($request);
		$array = call_user_func_array($callback, array(
			$response->getStatus(),
			$response->getHeaders(),
			$response->getBody()
			));

		return new Http\Response($array[0], $array[1], $array[2]);
	}

	/**
	 * Handles a {@link Ergo\Http\Request}, returns an {@link Ergo\Http\Response}
	 * @param Ergo\Http\Request
	 * @return Ergo\Http\Response
	 */
	public function execute($request)
	{
		return $this->controller->execute($request);
	}
}
