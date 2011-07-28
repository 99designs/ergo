<?php

namespace Ergo\Logging;

/**
 * A logger that writes to an internal array
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class ArrayLogger extends AbstractLogger
{
	private $_array;

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	public function log($message, $level=\Ergo\Logger::INFO)
	{
		$this->_array[] = array($message, $level, time());
		return $this;
	}

	/**
	 * Returns an array of logging messages
	 */
	public function toArray()
	{
		return $this->_array;
	}
}

