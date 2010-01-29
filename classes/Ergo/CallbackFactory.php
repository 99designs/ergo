<?php

class Ergo_CallbackFactory implements Ergo_Factory
{
	private $_callable;

	public function __construct($callable)
	{
		$this->_callable = $callable;
	}

	/**
	 * @return object
	 */
	public function create()
	{
		return call_user_func($this->_callable);
	}
}
