<?php


/**
 * A simple logger that uses a callback to process each message
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_CallbackLogger extends Ergo_Logging_AbstractLogger
{
	private $_callback;

	function __construct($callback, $level=Ergo_Logger::INFO)
	{
		parent::__construct($level);
		$this->_callback = $callback;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		if($this->_isLevelEnabled($level))
		{
			call_user_func($this->_callback,array('log',$message,$level));
		}
	}
}

