<?php

namespace Ergo\Logging;

/**
 * A simple logger that uses a callback to process each message
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class CallbackLogger extends AbstractLogger
{
	private $_callback;

	function __construct($callback, $level=\Ergo\Logger::INFO)
	{
		parent::__construct($level);
		$this->_callback = $callback;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		if($this->_isLevelEnabled($level))
			call_user_func($this->_callback,array('log',$message,$level));

		return $this;
	}
}

