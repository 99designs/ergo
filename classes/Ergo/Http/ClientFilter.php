<?php

/**
 * Filters an Http connection
 */
interface Ergo_Http_ClientFilter
{
	/**
	 * Called before the request is dispatched
	 * @return Ergo_Http_Request
	 */
	function request($request);

	/**
	 * Called before the response is returned
	 * @return Ergo_Http_Response
	 */
	function response($response);
}
