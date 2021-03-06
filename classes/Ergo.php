<?php

/**
 * The core object used as a locator for the {@link \Ergo\Application} object instance
 */
class Ergo
{
	private static $_application;
	private static $_starting;
	private static $_started;
	private static $_shutdownRegistered=false;
	private static $_autostart;

	/**
	 * Registers an application to be started when it's
	 * first called
	 */
	public static function autostart($application)
	{
		self::$_autostart = $application;
	}

	/**
	 * Starts a particular application instance
	 */
	public static function start($application)
	{
		self::$_application = $application;
		self::$_starting = true;
		self::$_started = false;
		$application->start();
		self::$_starting = false;
		self::$_started = true;
		self::$_autostart = null;

		// callback to clean up on process shutdown
		if(!self::$_shutdownRegistered)
		{
			register_shutdown_function(array(__CLASS__, 'shutdown'));
			self::$_shutdownRegistered = true;
		}
	}

	/**
	 * Stops the current application instance
	 */
	public static function stop()
	{
		self::$_application->stop();
		self::$_started = false;
	}

	/**
	 * Called as a shutdown function, calls stop() if required
	 */
	public static function shutdown()
	{
		if(self::$_started)
			self::stop();
	}

	/**
	 * Whether the application instance is started.
	 */
	public static function isStarted()
	{
		return self::$_started;
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
			if(!empty(self::$_autostart))
			{
				self::start(self::$_autostart);
			}
			else
			{
				throw new Exception('No application initialized');
			}
		}

		return self::$_application;
	}

	/**
	 * Delegates static calls to the internal {@link Application} object
	 */
	static function __callStatic($method, $arguments)
	{
		return call_user_func_array(
			array(self::application(), $method), $arguments);
	}
}
