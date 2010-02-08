<?php

/**
 * The foundation and central lookup mechanism for a web application, reference
 * by the static {@link Ergo} object
 */
class Ergo_Application implements Ergo_Plugin
{
	const REQUEST_FACTORY='request_factory';
	const LOGGER_FACTORY='logger_factory';
	const REGISTRY_DATETIME='datetime';

	protected $_registry;
	private $_mixin;
	private $_started=false;
	private $_errorHandler;
	private $_errorProxy;

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
			$this->_errorProxy = new Ergo_Error_ErrorProxy($this);
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
	 * Gets the logger factory used to create loggers
	 */
	public function loggerFactory()
	{
		if(!isset($this->_loggerFactory))
		{
			$this->_loggerFactory = new Ergo_Logging_DefaultLoggerFactory();
		}

		return $this->_loggerFactory;
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 * @chainable
	 */
	public function setLoggerFactory(Ergo_Logging_LoggerFactory $factory)
	{
		$this->_loggerFactory = $factory;
		return $this;
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
	 * Returns a request object for the current http request
	 */
	public function request()
	{
		return $this->requestFactory()->create();
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 */
	public function requestFactory()
	{
		return $this->genericFactory(self::REQUEST_FACTORY,
			new Ergo_Http_RequestFactory()
			);
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 */
	public function setRequestFactory(Ergo_Factory $factory)
	{
		return $this->genericFactory(self::REQUEST_FACTORY,
			new Ergo_Http_RequestFactory(),
			$factory
			);
	}

	/**
	 * Returns the Ergo_Error_ErrorProxy for the application
	 */
	public function errorProxy()
	{
		return $this->_errorProxy;
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
	 * Gets the error handler for the application
	 * @return object
	 */
	public function errorHandler()
	{
		return $this->_errorHandler;
	}

	/**
	 * Sets the error handler for the application
	 * @chainable
	 */
	public function setErrorHandler($errorHandler)
	{
		$this->_errorHandler = $errorHandler;
		return $this;
	}

	/* (non-phpdoc)
	 * @see http://www.php.net/manual/en/language.oop5.overloading.php
	 */
	public function __call($method, $parameters)
	{
		return $this->mixin()->__call($method, $parameters);
	}

	/**
	 * Provides a generic factory method that has a default, an optional
	 * provided instance to use instead. Objects are stored in the registry.
	 */
	protected function genericFactory($key, $default, $provided=null)
	{
		$handle = $this->registry()->handle($key);

		if(isset($provided)) $handle->set($provided);

		return $handle->exists() ? $handle->get() : $handle->set($default);
	}

	/**
	 * Returns the current timestamp of the instance returned by {@link dateTime()}
	 * @return int
	 */
	public function time()
	{
		return (int) $this->dateTime()->format('U');
	}

	/**
	 * Returns the current {@link DateTime} instance in the registry, or creates a new one
	 * @return object
	 */
	public function dateTime()
	{
		return $this->registry()->isRegistered(Ergo_Application::REGISTRY_DATETIME)
			? $this->lookup(Ergo_Application::REGISTRY_DATETIME)
			: new DateTime('now')
			;
	}

	/**
	 * Sets a {@link DateTime} instance in the registry, for subsequent {@link dateTime()}
	 * and {@link time()} calls
	 * @chainable
	 */
	public function setDateTime($dateTime)
	{
		$this->register(Spf::REGISTRY_DATETIME, $dateTime, true);
		return $this;
	}
}
