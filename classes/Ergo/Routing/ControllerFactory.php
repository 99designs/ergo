<?php

interface Ergo_Routing_ControllerFactory
{
	/**
	 * @param $name
	 * @return Ergo_Routing_Controller
	 */
	public function createController($name);

}
