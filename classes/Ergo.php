<?php

/**
 * The core object used as a locator for the {@link Ergo_Application} object instance
 */
class Ergo
{
	private static $_application;
	private static $_starting;

	/**
	 * Starts a particular application instance
	 */
	public static function start($application)
	{
		self::$_application = $application;
		self::$_starting = true;
		$application->start();
		self::$_starting = false;
	}

	/**
	 * Stops the current application instance
	 */
	public static function stop()
	{
		self::$_application->stop();
	}

	/**
	 * Whether the application instance is started.
	 */
	public static function isStarted()
	{
		return !is_null(self::$_application);
	}

	/**
	 * Whether the application instance is currently starting.
	 */
	public static function isStarting()
	{
		return self::$_starting;
	}

	/**
	 * Gets the current application instance
	 */
	public static function application()
	{
		if(!isset(self::$_application))
		{
			throw new Ergo_Exception('No application initialized');
		}

		return self::$_application;
	}

	/**
	 * Looks up a config key in the current application config
	 */
	public static function config($key)
	{
		return self::application()->config($key);
	}

	/**
	 * Gets the current application registry
	 */
	public static function registry()
	{
		return self::application()->registry();
	}

	/**
	 * Gets a logger for a class or filename from the current application
	 */
	public static function loggerFor($class)
	{
		return self::application()->loggerFor($class);
	}

	/**
	 * Looks up a key in the current application registry
	 */
	public static function lookup($key)
	{
		return self::application()->lookup($key);
	}

	/**
	 * Looks up the front controller object for an application
	 */
	public static function controller()
	{
		return self::application()->controller();
	}

	/**
	 * Looks up the front controller object for an application
	 */
	public static function request()
	{
		return self::application()->request();
	}
}
