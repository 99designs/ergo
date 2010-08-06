<?php


/**
 * A logger that drops all log messages
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_NullLogger extends Ergo_Logging_AbstractLogger
{
	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		return $this;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::logException()
	 */
	function logException($exception,$level=Ergo_Logger::ERROR)
	{
		return $this;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::setLogLevel()
	 */
	function setLogLevel($level)
	{
		return $this;
	}
}