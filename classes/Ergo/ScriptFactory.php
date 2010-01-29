<?php

/**
 * A factory that uses and Ergo_Script to generate an object
 */
class Ergo_ScriptFactory implements Ergo_Factory
{
	private $_script;

	/**
	 * Construct
	 */
	public function __construct($script)
	{
		$this->_script = $script;
	}

	/**
	 * @return object
	 */
	public function create()
	{
		return $this->script->execute();
	}
}
