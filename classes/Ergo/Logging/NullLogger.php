<?php

namespace Ergo\Logging;

/**
 * A logger that drops all log messages
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class NullLogger extends AbstractLogger
{
	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::logException()
	 */
	function logException($exception,$level=\Ergo\Logger::ERROR)
	{
		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::setLogLevel()
	 */
	function setLogLevel($level)
	{
		return $this;
	}
}