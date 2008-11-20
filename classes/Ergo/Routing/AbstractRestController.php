<?php

/**
 * A controller for handling HTTP requests against a resource.
 */
abstract class Ergo_Routing_AbstractRestController
	extends Ergo_Routing_AbstractController
{
	/* (non-phpdoc)
	 * @see Ergo_Routing_Controller::execute()
	 */
	public function execute($request)
	{
		$method = strtolower($request->getRequestMethod());
		if(!in_array($method,array('get','put','post','delete','head')))
		{
			throw new Ergo_Routing_Exception("Unknown HTTP verb");
		}

		$this->setRouteMap($request->getRouteMap());

		return $this->$method($request);
	}

	/**
	 * Handles an HTTP GET request.
	 * @param Ergo_Http_Request
	 * @return Ergo_Http_Response
	 */
	public function get($request)
	{
		throw new Ergo_Http_Error_MethodNotAllowed('GET');
	}

	/**
	 * Handles an HTTP HEAD request.
	 * @param Ergo_Http_Request
	 * @return Ergo_Http_Response
	 */
	public function head($request)
	{
		throw new Ergo_Http_Error_MethodNotAllowed('HEAD');
	}

	/**
	 * Handles an HTTP POST request.
	 * @param Ergo_Http_Request
	 * @return Ergo_Http_Response
	 */
	public function post($request)
	{
		throw new Ergo_Http_Error_MethodNotAllowed('POST');
	}

	/**
	 * Handles an HTTP PUT request.
	 * @param Ergo_Http_Request
	 * @return Ergo_Http_Response
	 */
	public function put($request)
	{
		throw new Ergo_Http_Error_MethodNotAllowed('PUT');
	}

	/**
	 * Handles an HTTP DELETE request.
	 * @param Ergo_Http_Request
	 * @return Ergo_Http_Response
	 */
	public function delete($request)
	{
		throw new Ergo_Http_Error_MethodNotAllowed('DELETE');
	}
}
