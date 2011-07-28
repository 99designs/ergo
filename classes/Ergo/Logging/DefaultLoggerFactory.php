<?php

namespace Ergo\Logging;

/**
 * A default logger factory that returns a logger multiplexer with no loggers
 */
class DefaultLoggerFactory implements LoggerFactory
{
	private $_logger;

	/**
	 * Returns the singleton logger instance
	 */
	public function logger()
	{
		if(!isset($this->_logger))
		{
			$this->_logger = new LoggerMultiplexer();
		}

		return $this->_logger;
	}

	/* (non-phpdoc)
	 * @see LoggerFactory::createLogger
	 */
	public function createLogger($class)
	{
		return $this->logger();
	}

	/**
	 * Adds a logger to the internal logger multiplexer
	 * @chainable
	 */
	public function addLoggers($loggers)
	{
		$this->logger()->addLoggers(func_get_args());
		return $this;
	}

	/**
	 * Clears all loggers
	 */
	public function clearLoggers()
	{
		unset($this->_logger);
		return $this;
	}
}
