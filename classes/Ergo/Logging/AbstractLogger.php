<?php

namespace Ergo\Logging;

use \Psr\Log\LogLevel;


/**
 * An abstract logger that provides named logging methods and basic level
 * filtering
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
abstract class AbstractLogger extends \Psr\Log\AbstractLogger
{
	private $_loglevel;

	/**
	 * Construct a logger with a default level of trace
	 */
	public function __construct($level=LogLevel::INFO)
	{
		$this->_loglevel = $level;
	}

	function logException($exception, $level=LogLevel::ERROR)
	{
		$this->log(sprintf(
			"%s '%s' in %s:%d",
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine()
			), $level);
	}

	/**
	 * Sets the current log level
	 */
	function setLogLevel($level)
	{
		$this->_loglevel = $level;
		return $this;
	}

	/**
	 * Gets the current log level
	 */
	function getLogLevel()
	{
		return $this->_loglevel;
	}

	/**
	 * Gets an integer that corresponds to the logging level
	 */
	protected function _getLevelInteger($level)
	{
		switch($level)
		{
			case LogLevel::DEBUG: return 1;
			case LogLevel::INFO: return 2;
			case LogLevel::NOTICE: return 3;
			case LogLevel::WARNING: return 4;
			case LogLevel::ERROR: return 5;
			case LogLevel::CRITICAL: return 6;
			case LogLevel::ALERT: return 7;
			case LogLevel::EMERGENCY: return 8;
		}
	}

	/**
	 * Returns true if the level is equal to or greater than the current
	 * log level
	 */
	protected function _isLevelEnabled($level)
	{
		return $this->_getLevelInteger($level) >=
			$this->_getLevelInteger($this->getLogLevel());
	}


	// --------------------------------------------------------------------
	// helpers to interact with php error handling

	/**
	 * An error handler
	 */
	public function handleError($errno, $errstr, $errfile, $errline, $context)
	{
		// ignore suppressed errors
		if (error_reporting() === 0) return;

		$error = new Error($errno);
		$this->error("PHP Error $error: $errstr in $errfile:$errline");
	}
}
