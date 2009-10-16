<?php

/**
 * An abstract logger that provides named logging methods and basic level
 * filtering
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
abstract class Ergo_Logging_AbstractLogger implements Ergo_Logger
{
	private $_loglevel;

	/**
	 * Construct a logger with a default level of trace
	 */
	public function __construct($level=Ergo_Logger::INFO)
	{
		$this->_loglevel = $level;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::logException()
	 */
	function logException($exception,$level=Ergo_Logger::ERROR)
	{
		$this->log(sprintf(
			"%s '%s' in %s:%d",
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine()
			), $level);
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::setLogLevel()
	 */
	function setLogLevel($level)
	{
		$this->_loglevel = $level;
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
			case Ergo_Logger::TRACE: return 1;
			case Ergo_Logger::INFO: return 2;
			case Ergo_Logger::WARN: return 3;
			case Ergo_Logger::ERROR: return 4;
			case Ergo_Logger::FATAL: return 5;
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
	// helpers to make logging more pleasant

	/**
	 * Logs a message of level INFO
	 */
	function info($message)
	{
		$args = func_get_args();
		$message = (count($args) > 1) ? call_user_func_array('sprintf',$args) : $message;
		$this->log($message, Ergo_Logger::INFO);
	}

	/**
	 * Logs a message of level TRACE
	 */
	function trace($message)
	{
		$args = func_get_args();
		$message = (count($args) > 1) ? call_user_func_array('sprintf',$args) : $message;
		$this->log($message, Ergo_Logger::TRACE);
	}

	/**
	 * Logs a message of level WARN
	 */
	function warn($message)
	{
		$args = func_get_args();
		$message = (count($args) > 1) ? call_user_func_array('sprintf',$args) : $message;
		$this->log($message, Ergo_Logger::WARN);
	}

	/**
	 * Logs a message of level ERROR
	 */
	function error($message)
	{
		$args = func_get_args();
		$message = (count($args) > 1) ? call_user_func_array('sprintf',$args) : $message;
		$this->log($message, Ergo_Logger::ERROR);
	}

	/**
	 * Logs a message of level FATAL
	 */
	function fatal($message)
	{
		$args = func_get_args();
		$message = (count($args) > 1) ? call_user_func_array('sprintf',$args) : $message;
		$this->log($message, Ergo_Logger::FATAL);
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

		$this->error(
			"PHP Error " . $this->_errorNumberString($errno) . ": ".
			$errstr . ' in '. $errfile . ':' .$errline
			);
	}

	/**
	 * Convert a php errorno into a string
	 */
	private function _errorNumberString($intval)
	{
		$errorlevels = array(
			2048 => 'E_STRICT',
			2047 => 'E_ALL',
			1024 => 'E_USER_NOTICE',
			512 => 'E_USER_WARNING',
			256 => 'E_USER_ERROR',
			128 => 'E_COMPILE_WARNING',
			64 => 'E_COMPILE_ERROR',
			32 => 'E_CORE_WARNING',
			16 => 'E_CORE_ERROR',
			8 => 'E_NOTICE',
			4 => 'E_PARSE',
			2 => 'E_WARNING',
			1 => 'E_ERROR'
			);

		return $errorlevels[$intval];
	}

}

?>
