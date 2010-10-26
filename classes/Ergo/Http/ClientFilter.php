<?php

namespace Ergo\Http;

/**
 * Filters an Http connection
 */
interface ClientFilter
{
	/**
	 * Called before the request is dispatched
	 * @return Request
	 */
	function request($request);

	/**
	 * Called before the response is returned
	 * @return Response
	 */
	function response($response);
}
