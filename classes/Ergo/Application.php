<?php

/**
 * The foundation and central lookup mechanism for a web application, reference
 * by the static {@link Ergo} object
 */
class Ergo_Application extends Ergo_Mixin implements Ergo_Plugin
{
	protected $_registry;
	protected $_loggerFactory;

	private $_mixin;
	private $_started=false;
	private $_errorHandler;

	/**
	 * Template method, called when the application starts
	 */
	public function onStart()
	{
	}

	/**
	 * Template method, called when the application stops
	 */
	public function onStop()
	{
	}


	/* (non-phpdoc)
	 * @see Ergo_Plugin::start()
	 */
	public function start()
	{
		if($this->_started==false)
		{
			$this->onStart();
			foreach($this->plugins() as $plugin) $plugin->start();
			$this->_started = true;
		}
	}

	/* (non-phpdoc)
	 * @see Ergo_Plugin::stop()
	 */
	public function stop()
	{
		if($this->_started)
		{
			$this->onStop();
			foreach($this->plugins() as $plugin) $plugin->stop();
		}
	}

	/**
	 * Resets all internal state
	 */
	public function reset()
	{
		unset($this->_loggerFactory);
		unset($this->_registry);
		unset($this->_mixin);
		unset($this->_errorHandler);
		return $this;
	}

	/**
	 * Gets the application's core registry
	 */
	public function registry()
	{
		if(!isset($this->_registry))
		{
			$this->_registry = new Ergo_Registry();
		}

		return $this->_registry;
	}

	/**
	 * Looks up a registry key value, requires a 'config' object to be
	 * in the registry
	 */
	public function config($key)
	{
		return $this->lookup('config')->get($key);
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 */
	public function loggerFactory(Ergo_Logging_LoggerFactory $factory=null)
	{
		if(!is_null($factory))
		{
			$this->_loggerFactory = $factory;
		}
		else if(!isset($this->_loggerFactory))
		{
			$this->_loggerFactory = new Ergo_Logging_DefaultLoggerFactory();
		}

		return $this->_loggerFactory;
	}

	/**
	 * Looks up a logger for a class or filename from the logger factory
	 */
	public function loggerFor($class)
	{
		return $this->loggerFactory()->createLogger($class);
	}

	/**
	 * Looks up a key in the application's core registry
	 */
	public function lookup($key)
	{
		return $this->registry()->lookup($key);
	}

	/**
	 * Returns an applications central controller for executing requests
	 */
	public function controller()
	{
		return new Ergo_Routing_RoutedController();
	}

	/**
	 * Returns the {@link Ergo_Mixin} instance used for plugins
	 */
	protected function mixin()
	{
		if(!isset($this->_mixin))
		{
			$this->_mixin = new Ergo_Mixin();
		}

		return $this->_mixin;
	}

	/**
	 * Returns the plugins plugged into the application
	 */
	public function plugins()
	{
		return $this->mixin()->delegates();
	}

	/**
	 * Adds a {@link Ergo_Plugin} to the application
	 */
	public function plug(Ergo_Plugin $plugin)
	{
		$this->mixin()->addDelegate($plugin);
		return $this;
	}

	/**
	 * Determines if the application is running in console mode
	 * @return bool
	 */
	public function isConsole()
	{
		return (php_sapi_name() == 'cli');
	}

	/**
	 * Gets the error handler for the application, or sets one if provided
	 * @return object
	 */
	public function errorHandler($errorHandler=false)
	{
		if($errorHandler !== false)
		{
			$this->_errorHandler = $errorHandler;
		}
		return $this->_errorHandler;
	}

	/* (non-phpdoc)
	 * @see http://www.php.net/manual/en/language.oop5.overloading.php
	 */
	public function __call($method, $parameters)
	{
		return $this->mixin()->__call($method, $parameters);
	}
}
