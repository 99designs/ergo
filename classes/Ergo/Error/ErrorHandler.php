<?php

/**
 * A basic handler for PHP errors and exceptions which consolidates errors
 * into error exceptions provides for simple logging and error formatting.
 */
interface Ergo_Error_ErrorHandler
{
	/**
	 * Registers the error handler
	 * @chainable
	 */
	function register();

	/**
	 * Attempts to unregister the error handler
	 * @chainable
	 */
	function unregister();

	/**
	 * Handles an exception or error
	 */
	public function handle($e);

	/**
	 * Returns logger attached to the error handler
	 * @return object an Ergo_Logger
	 */
	public function logger();

	/**
	 * Returns an array of key value pairs describing the error context
	 * @return array
	 */
	public function context();
}

