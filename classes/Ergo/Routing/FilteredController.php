<?php

namespace Ergo\Routing;

/**
 * A controller that supports filtering requests and responses and delegates
 * to another controller for processing
 */
class FilteredController extends AbstractController
{
	private $_controller;
	private $_requests = array();
	private $_responses = array();

	/**
	 * Constructor
	 * @param Controller a delegate controller
	 */
	public function __construct($controller)
	{
		$this->_controller = $controller;
	}

	/**
	 * Adds an callback for processing the request before it is processed, must return a request
	 * @chainable
	 */
	public function addRequestFilter($filter)
	{
		$this->_requests []= $filter;
		return $this;
	}

	/**
	 * Adds an callback for processing the response after it is processed, must return a response
	 * @chainable
	 */
	public function addResponseFilter($filter)
	{
		$this->_responses []= $filter;
		return $this;
	}

	/**
	 * Apply a chain of filters to an object
	 * @return object
	 */
	private function _filter($chain, $object)
	{
		foreach($chain as $filter)
		{
			if(!$object = call_user_func($filter, $object))
				throw new Exception("Filter returned null");
		}

		return $object;
	}

	/* (non-phpdoc)
	 * @see Controller::execute()
	 */
	public function execute($request)
	{
		return $this->_filter($this->_responses,
			$this->_controller->execute(
				$this->_filter($this->_requests, $request)
				));
	}
}
