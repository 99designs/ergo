<?php

namespace Ergo\Routing;

interface ControllerFactory
{
	/**
	 * @param $name
	 * @return Controller
	 */
	public function createController($name);

}
