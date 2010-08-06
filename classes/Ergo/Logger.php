<?php

/**
 * A simple logging interface
 */
interface Ergo_Logger
{
	/**
	 * These imply a hierarchy
	 */
	const TRACE='trace';
	const INFO='info';
	const WARN='warn';
	const ERROR='error';
	const FATAL='fatal';

	/**
	 * Logs a message
	 * @chainable
	 */
	function log($message,$level=Ergo_Logger::INFO);

	/**
	 * Logs an exception
	 * @chainable
	 */
	function logException($exception,$level=Ergo_Logger::ERROR);

	/**
	 * Sets the log level to display equal to and above
	 * @chainable
	 */
	function setLogLevel($level);
}
