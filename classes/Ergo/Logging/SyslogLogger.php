<?php

/**
 * A logger that writes to a file
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_SyslogLogger extends Ergo_Logging_AbstractLogger
{
	/**
	 * Construct
	 */
	function __construct($identifier, $options=false, $facility=LOG_USER)
	{
		openlog($identifier, $options, $facility);
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		syslog($this->_levelToPriority($level), $message);
		return $this;
	}

	/**
	 * Converts a logging level to a syslog priority
	 */
	private function _levelToPriority($level)
	{
		switch($level)
		{
			case Ergo_Logger::TRACE: return LOG_DEBUG;
			case Ergo_Logger::INFO: return LOG_INFO;
			case Ergo_Logger::WARN: return LOG_WARNING;
			case Ergo_Logger::ERROR: return LOG_ERR;
			case Ergo_Logger::FATAL: return LOG_CRIT;
		}
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::logException()
	 */
	public function logException($exception,$level=Ergo_Logger::ERROR)
	{
		$message = sprintf("exception '%s' with message '%s' in %s:%d",
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine()
			);

		$this->log($message, $level);
		return $this;
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		closelog();
	}
}
