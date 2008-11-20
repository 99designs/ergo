<?php

/**
 * A default logger factory that returns a basic console logger that
 * will show >= INFO messages.
 */
class Ergo_Logging_DefaultLoggerFactory implements Ergo_Logging_LoggerFactory
{
	private $_ansiColor;
	private $_level;
	
	/**
	 * Constructor
	 */
	function __construct($ansiColor=false, $level=Ergo_Logger::INFO)
	{
		$this->_ansiColor = $ansiColor;
		$this->_level = $level;
	}
	
	/* (non-phpdoc)
	 * @see Ergo_LoggerFactory::createLogger
	 */
	function createLogger($class)
	{
		return new Ergo_Logging_ConsoleLogger($this->_ansiColor, $this->_level);
	}
}
