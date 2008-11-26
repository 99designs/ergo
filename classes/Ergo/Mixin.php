<?php

/**
 * A class that promotes composition by collecting mixin objects and delegating
 * calls to those objects as though they were one object
 */
class Ergo_Mixin
{
	private $_delegates=array();
	private $_callmap=array();

	/**
	 * @chainable
	 */
	public function addDelegate($object)
	{
		$this->_delegates[]=$object;
		$this->_callmap = array();
		return $this;
	}

	/**
	 *
	 */
	public function delegates()
	{
		return $this->_delegates;
	}

	/**
	 *
	 */
	public function isCallable($method)
	{
		foreach($this->_delegates as $delegate)
		{
			if(method_exists($delegate,$method)) return true;
		}

		return false;
	}

	/* (non-phpdoc)
	 * @see http://www.php.net/manual/en/language.oop5.overloading.php
	 */
	public function __call($method, $parameters)
	{
		if(!isset($this->_callmap[$method]))
		{
			foreach($this->_delegates as $delegate)
			{
				if(method_exists($delegate,$method))
				{
					$this->_callmap[$method] = $delegate;
					break;
				}
			}
		}

		if(!isset($this->_callmap[$method]))
		{
			throw new BadMethodCallException("No delegates with method '$method'");
		}

		return call_user_func_array(array(
			$this->_callmap[$method],
			$method
			), $parameters);
	}
}

