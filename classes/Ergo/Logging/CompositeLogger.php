<?php

namespace Ergo\Logging;

/**
 * A logger that is composed of other loggers
 */
interface CompositeLogger extends \Ergo\Logger
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
