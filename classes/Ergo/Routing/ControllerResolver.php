<?php

namespace Ergo\Routing;

interface ControllerResolver
{
	/**
	 * @param $name
	 * @return Controller
	 */
	public function resolve($name);

}
