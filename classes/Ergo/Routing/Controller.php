<?php

/**
 * A controller for handling a routed request
 */
interface Ergo_Routing_Controller
{
	/**
	* Handles a {@link Ergo_Routing_RoutedHttpRequest}, returns a response
	 * @param Ergo_Routing_RoutedHttpRequest
	 * @return Ergo_Http_Response
	 */
	public function execute($request);
}
