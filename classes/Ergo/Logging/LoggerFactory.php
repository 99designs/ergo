<?php

/**
 * A factory for creating loggers for particular classes
 */
interface Ergo_Logging_LoggerFactory
{
	/**
	 * Creates a logger instance for a class
	 * @return object Ergo_Logger
	 */
	function createLogger($class);
}
