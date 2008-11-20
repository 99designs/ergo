<?php

/**
 * A configuration object which provides an interface to key-based configuration
 * information for an application
 */
interface Ergo_Config
{
	/**
	 * Gets a key from the configuration.
	 */
	function get($key);

	/**
	 * Determines whether a particular key exists
	 */
	function exists($key);

	/**
	 * Gets all configuration keys
	 */
	function keys();
}
