<?php

namespace Ergo;

/**
 * The foundation and central lookup mechanism for a web application, reference
 * by the static {@link Ergo} object
 */
class Application implements Plugin
{
	const REQUEST_FACTORY='request_factory';
	const LOGGER_FACTORY='logger_factory';
	const REGISTRY_DATETIME='datetime';
	const REGISTRY_ROUTER='router';

	protected $_registry;
	private $_mixin;
	private $_started=false;
	private $_errorHandler;
	private $_errorProxy;
	private $_classLoader;
	private $_middleware=array();

	/**
	 * Constructor
	 */
	public function __construct($classLoader=null)
	{
		$this->_classLoader = $classLoader;
	}

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

	/**
	 * Returns whether this application instance has been started
	 * @return bool
	 */
	protected function isStarted()
	{
		return $this->_started;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Plugin::start()
	 */
	public function start()
	{
		if($this->_started==false)
		{
			$this->_errorProxy = new Error\ErrorProxy($this);
			$this->onStart();
			foreach($this->plugins() as $plugin) $plugin->start();
			$this->_started = true;
		}
	}

	/* (non-phpdoc)
	 * @see \Ergo\Plugin::stop()
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
		unset($this->_middleware);
		return $this;
	}

	/**
	 * Gets the application's core registry
	 */
	public function registry()
	{
		if(!isset($this->_registry))
		{
			$this->_registry = new Registry();
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
			$this->_loggerFactory = new Logging\DefaultLoggerFactory();
		}

		return $this->_loggerFactory;
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 * @chainable
	 */
	public function setLoggerFactory(Logging\LoggerFactory $factory)
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
	 * @see Ergo\Registry::lookup()
	 */
	public function lookup($key, $closure=null)
	{
		return $this->registry()->lookup($key, $closure);
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
		return $this->lookup(self::REQUEST_FACTORY, function() {
			return new Http\RequestFactory($_SERVER);
		});
	}

	/**
	 * Creates or sets the logger factory used to create loggers
	 */
	public function setRequestFactory(Factory $factory)
	{
		$this->register(self::REQUEST_FACTORY, $factory);
	}

	/**
	 * Returns the \Ergo\Error\ErrorProxy for the application
	 */
	public function errorProxy()
	{
		return $this->_errorProxy;
	}

	/**
	 * Returns the {@link \Ergo\Mixin} instance used for plugins
	 */
	protected function mixin()
	{
		if(!isset($this->_mixin))
		{
			$this->_mixin = new Mixin();
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
	 * Adds a {@link \Ergo\Plugin} to the application
	 */
	public function plug(\Ergo\Plugin $plugin)
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
		return $this->registry()->isRegistered(self::REGISTRY_DATETIME)
			? $this->lookup(self::REGISTRY_DATETIME)
			: new \DateTime('now')
			;
	}

	/**
	 * Sets a {@link DateTime} instance in the registry, for subsequent {@link dateTime()}
	 * and {@link time()} calls
	 * @chainable
	 */
	public function setDateTime($dateTime)
	{
		$this->registry()->register(self::REGISTRY_DATETIME, $dateTime, true);
		return $this;
	}

	/**
	 * Returns an application's central controller for executing requests
	 */
	public function controller()
	{
		return new Routing\FilteredController($this->router());
	}

	/**
	 * Returns a request router
	 * @return Router
	 */
	public function router()
	{
		return $this->registry()->lookup(self::REGISTRY_ROUTER, function(){
			return new \Ergo\Routing\Router();
		});
	}

	/**
	 * Returns the class loader associateds with the application
	 * @return Router
	 */
	public function classLoader()
	{
		if(!isset($this->_classLoader))
		{
			$this->_classLoader = new ClassLoader();
		}

		return $this->_classLoader;
	}

	/**
	 * Adds a middleware to the end of the middleware stack
	 * @chainable
	 */
	public function middleware($className)
	{
		$this->_middleware []= $className;
		return $this;
	}

	/**
	 * Processes an HTTP request, copies response to STDOUT
	 * @return void
	 */
	public function run($server=null, $stream=null)
	{
		$server = $server ?: $_SERVER;
		$stream = $stream ?: fopen('php://output','w');
		$controller = $this->controller();

		// build up wrappers of middleware
		foreach($this->_middleware as $middleware)
			$controller = new $middleware($controller, $this);

		$requestFactory = new Http\RequestFactory($server);
		$response = $controller->execute($requestFactory->create());
		$sender = new Http\ResponseSender($response, $stream);
		$sender->send();
	}
}
