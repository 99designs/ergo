<?php

/**
 * A strategy for handling uncaught exceptions
 */
interface Ergo_ExceptionStrategy
{
	/**
	 * Handle an uncaught exception, errors are
	 */
	public function handleException($e);
}
