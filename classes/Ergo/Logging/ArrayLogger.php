<?php
/**
 * A logger that writes to an internal array
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_ArrayLogger extends Ergo_Logging_AbstractLogger
{
	private $_array;

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	public function log($message, $level=Ergo_Logger::INFO)
	{
		$this->_array[] = array($message, $level, time());
	}

	/**
	 * Returns an array of logging messages
	 */
	public function toArray()
	{
		return $this->_array;
	}
}

