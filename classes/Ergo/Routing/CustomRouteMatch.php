<?php

namespace Ergo\Routing;

/**
* A custom matcher callback for matching routes.
 */
interface CustomRouteMatch
{
	public function routeMatch($url);
}
