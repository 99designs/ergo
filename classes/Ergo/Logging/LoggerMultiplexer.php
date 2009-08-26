<?php


/**
 * A basic implementation of a composite logger that multiplexes log messages to many loggers
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_LoggerMultiplexer
	extends Ergo_Logging_AbstractLogger
	implements Ergo_Logging_CompositeLogger
{
	private $_loggers=array();

	/**
	 * Constructor
	 */
	function __construct($loggers=array(), $level=Ergo_Logger::INFO)
	{
		parent::__construct($level);
		$this->addLoggers($loggers);
	}

	/* (non-phpdoc)
	 * @see Ergo_Logging_CompositeLogger::addLoggers()
	 */
	function addLoggers($loggers)
	{
		foreach(func_get_args() as $logger)
		{
			if(is_array($logger))
			{
				foreach($logger as $sublogger)
				{
					$this->addLoggers($sublogger);
				}
			}
			else if(is_object($logger))
			{
				$this->_loggers[] = $logger;
			}
		}

		return $this;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logging_CompositeLogger::clearLoggers()
	 */
	function clearLoggers()
	{
		$this->_loggers = array();
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
}
