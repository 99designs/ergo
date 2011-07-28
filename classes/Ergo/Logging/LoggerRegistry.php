<?php

namespace Ergo\Logging;

/**
 * A logger factory that can have implementations registered
 */
class LoggerRegistry implements LoggerFactory
{
	private $_default;
	private $_loggers;

	/**
	 * Constructor
	 */
	function __construct(\Ergo\Logger $defaultLogger)
	{
		$this->_default = $defaultLogger;
	}

	/* (non-phpdoc)
	 * @see \Ergo\LoggerFactory::createLogger
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
	function registerLogger($class, \Ergo\Logger $defaultLogger)
	{
		throw new \BadMethodCallException("Not implemented");
	}
}
