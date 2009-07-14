<?php

/**
 * A logger that is composed of other loggers
 */
interface Ergo_Logging_CompositeLogger extends Ergo_Logger
{
	/**
	 * Adds either a single logger, an array of loggers or multiple logger
	 * arguments
	 * @chainable
	 */
	function addLoggers($logger);

	/**
	 * Clears all loggers previously added
	 * @chainable
	 */
	function clearLoggers();
}
