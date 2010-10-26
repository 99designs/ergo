<?php

namespace Ergo;

/**
 * A simple wrapper around a php script
 */
class Script
{
	private $_path;

	public function __construct($path)
	{
		$this->_path = $path;
	}

	public function execute()
	{
		// TODO: generate errors if this file doesn't exist
		return include($this->_path);
	}

	public function __invoke()
	{
		return $this->execute();
	}
}
