<?php

namespace Ergo\Logging;

/**
 * A factory for creating loggers for particular classes
 */
interface LoggerFactory
{
	/**
	 * Creates a logger instance for a class
	 * @return object \Psr\Log\LoggerInterface
	 */
	function createLogger($class);
}
