<?php


/**
 * A logger that sends log messages to different logging backends
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_LoggerMultiplexer extends Ergo_Logging_AbstractLogger
{
	private $_loggers=array();

	/**
	 * Constructor
	 */
	function __construct($loggers=array())
	{
		foreach((array)$loggers as $logger) $this->addLogger($logger);
	}

	/**
	 * Adds a logger to recieve logging messages
	 * @chainable
	 */
	function addLogger($logger)
	{
		$logger->setLogLevel($this->getLogLevel());
		$this->_loggers[] = $logger;
		return $this;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		foreach($this->_loggers as &$logger)
		{
			$logger->log($message, $level);
		}
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::logException()
	 */
	function logException($exception,$level=Ergo_Logger::ERROR)
	{
		foreach($this->_loggers as &$logger)
		{
			$logger->logException($exception,$level);
		}
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::setLogLevel()
	 */
	function setLogLevel($level)
	{
		parent::setLogLevel($level);

		foreach($this->_loggers as &$logger)
		{
			$logger->setLogLevel($level);
		}
	}

	/**
	 * Clears all loggers added
	 * @chainable
	 */
	function clear()
	{
		$this->_loggers = array();
	}
}
