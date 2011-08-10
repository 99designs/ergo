<?php

namespace Ergo\Error;

/**
 * A basic handler for exceptions with simple logging and error formatting.
 */
interface ErrorHandler
{
	/**
	 * Handles an exception or error
	 */
	public function handle($e);

	/**
	 * Returns logger attached to the error handler
	 * @return object an \Ergo\Logging\CompositeLogger
	 */
	public function logger();
}

