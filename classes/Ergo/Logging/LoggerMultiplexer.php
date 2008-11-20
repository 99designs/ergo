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
	 */
	function addLogger($logger)
	{
		$logger->setLogLevel($this->getLogLevel());
		$this->_loggers[] = $logger;
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
}
