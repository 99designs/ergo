<?php

/**
 * A default logger factory that returns a logger multiplexer with no loggers
 */
class Ergo_Logging_DefaultLoggerFactory
	implements Ergo_Logging_LoggerFactory
{
	private $_logger;

	/**
	 * Returns a single logger instance
	 */
	protected function logger()
	{
		if(!isset($this->_logger))
		{
			$this->_logger = new Ergo_Logging_LoggerMultiplexer();
		}

		return $this->_logger;
	}

	/* (non-phpdoc)
	 * @see Ergo_LoggerFactory::createLogger
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
