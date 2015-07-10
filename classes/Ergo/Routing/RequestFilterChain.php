<?php

namespace Ergo\Routing;

class RequestFilterChain
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
				throw new Exception("Filter returned a null request");
			}
		}

		return $request;
	}

	public function addFilter($filter)
	{
		$this->_filters[] = $filter;
		return $this;
	}
}
