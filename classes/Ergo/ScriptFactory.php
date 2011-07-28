<?php

namespace Ergo;

/**
 * A factory that uses and \Ergo\Script to generate an object
 */
class ScriptFactory implements Factory
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
