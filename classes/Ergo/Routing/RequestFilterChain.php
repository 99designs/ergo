<?php

class Ergo_Routing_RequestFilterChain
{
	private $_filters=array();

	/**
	 * Filter a request, fail if a null result is returned
	 */
	public function filter($request)
	{
		foreach($this->_filters as $filter)
		{
			if(!$request = $filter->filter($request))
			{
				throw new Ergo_Routing_Exception(
					"Filter returned a null request");
			}
		}

		return $request;
	}

	public function addFilter(Ergo_Routing_RequestFilter $filter)
	{
		$this->_filters[] = $filter;
		return $this;
	}
}
