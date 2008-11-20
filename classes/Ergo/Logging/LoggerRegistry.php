<?php


/**
 * A logger factory that can have implementations registered
 */
class Ergo_Logging_LoggerRegistry implements Ergo_Logging_LoggerFactory
{
	private $_default;
	private $_loggers;

	/**
	 * Constructor
	 */
	function __construct(Ergo_Logger $defaultLogger)
	{
		$this->_default = $defaultLogger;
	}

	/* (non-phpdoc)
	 * @see Ergo_LoggerFactory::createLogger
	 */
	function createLogger($class)
	{
		$class = is_object($class) ? get_class($class) : $class;
		$class = strtolower($class);

		if(isset($_loggers[$class]))
		{
			return $_loggers[$class];
		}
		else
		{
			return $this->_default;
		}
	}

	/**
	 * Register a logger for a specific class name, at present this classname
	 * must match exactly
	 */
	function registerLogger($class, Ergo_Logger $defaultLogger)
	{
		throw new BadMethodCallException("Not implemented");
	}
}
