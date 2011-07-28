<?php

namespace Ergo\Routing;

/**
 * A controller for handling a routed request
 */
interface Controller
{
	/**
	 * Handles a {@link RoutedHttpRequest}, returns a response
	 * @param Ergo\Http\RoutedHttpRequest
	 * @return Ergo\Http\Response
	 */
	public function execute($request);
}
