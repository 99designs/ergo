<?php

namespace Ergo\Logging;

/**
 * A logger that writes to the system log facility
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class SyslogLogger extends AbstractLogger
{
	/**
	 * Construct
	 */
	function __construct($identifier, $options=false, $facility=LOG_USER)
	{
		openlog($identifier, $options, $facility);
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
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
			case \Ergo\Logger::TRACE: return LOG_DEBUG;
			case \Ergo\Logger::INFO: return LOG_INFO;
			case \Ergo\Logger::WARN: return LOG_WARNING;
			case \Ergo\Logger::ERROR: return LOG_ERR;
			case \Ergo\Logger::FATAL: return LOG_CRIT;
		}
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::logException()
	 */
	public function logException($exception,$level=\Ergo\Logger::ERROR)
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
