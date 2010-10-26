<?php

namespace Ergo\Routing;

use Ergo\Http;

/**
 * A controller for handling HTTP requests against a resource.
 */
abstract class AbstractRestController extends AbstractController
{
	/* (non-phpdoc)
	 * @see Controller::execute()
	 */
	public function execute($request)
	{
		$method = strtolower($request->getRequestMethod());
		if(!in_array($method,array('get','put','post','delete','head')))
		{
			throw new Exception("Unknown HTTP verb");
		}

		$this->setRouteMap($request->getRouteMap());

		return $this->$method($request);
	}

	/**
	 * Handles an HTTP GET request.
	 * @param Request
	 * @return Response
	 */
	public function get($request)
	{
		throw new Http\Error\MethodNotAllowed('GET');
	}

	/**
	 * Handles an HTTP HEAD request.
	 * @param Http\Request
	 * @return Http\Response
	 */
	public function head($request)
	{
		throw new Http\Error\MethodNotAllowed('HEAD');
	}

	/**
	 * Handles an HTTP POST request.
	 * @param Http\Request
	 * @return Http\Response
	 */
	public function post($request)
	{
		throw new Http\Error\MethodNotAllowed('POST');
	}

	/**
	 * Handles an HTTP PUT request.
	 * @param Http\Request
	 * @return Http\Response
	 */
	public function put($request)
	{
		throw new Http\Error\MethodNotAllowed('PUT');
	}

	/**
	 * Handles an HTTP DELETE request.
	 * @param Http\Request
	 * @return Http\Response
	 */
	public function delete($request)
	{
		throw new Http\Error\MethodNotAllowed('DELETE');
	}
}
