<?php

/**
 * A default logger factory that returns a logger multiplexer with no loggers
 */
class Ergo_Logging_DefaultLoggerFactory implements Ergo_Logging_LoggerFactory
{
	private $_logger;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->_logger = new Ergo_Logging_LoggerMultiplexer();
	}

	/* (non-phpdoc)
	 * @see Ergo_LoggerFactory::createLogger
	 */
	public function createLogger($class)
	{
		return $this->_logger;
	}

	/**
	 * Adds a logger to the internal logger multiplexer
	 */
	public function addLogger($logger)
	{
		$this->_logger->addLogger($logger);
		return $this;
	}

	/**
	 * Adds a logger to the internal logger multiplexer
	 */
	public function addLoggers($loggers)
	{
		if(!is_array($loggers)) $loggers = func_get_args();

		foreach($loggers as $logger) $this->addLogger($logger);

		return $this;
	}

	/**
	 * Clears all loggers
	 */
	public function clear()
	{
		$this->_logger->clear();
		return $this;
	}
}
