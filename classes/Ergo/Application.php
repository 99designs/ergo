<?php

/**
 * The foundation and central lookup mechanism for a web application, reference
 * by the static {@link Ergo} object
 */
class Ergo_Application implements Ergo_Plugin
{
	protected $_registry;
	private $_plugins=array();
	private $_callmap=array();
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
	public function reset($event)
	{
		if(!isset($this->_registry))
		{
			unset($this->_registry);
		}

		$this->_plugins = array();
		$this->_callmap = array();
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
	 * Looks up a logger for a class or filename, requires a 'loggerfactory'
	 * to be in the registry
	 */
	public function loggerFor($class)
	{
		return $this->lookup('loggerfactory')->createLogger($class);
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
	 * Returns the plugins plugged into the application
	 */
	public function plugins()
	{
		return $this->_plugins;
	}

	/**
	 * Adds a {@link Ergo_Plugin} to the application
	 */
	public function plug(Ergo_Plugin $plugin)
	{
		$this->_callmap = array();
		$this->_plugins[] = $plugin;
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
		// first check the call map
		if(!isset($this->_callmap[$method]))
		{
			foreach($this->plugins() as $plugin)
			{
				if(method_exists($plugin,$method))
				{
					$this->_callmap[$method] = $plugin;
					break;
				}
			}
		}

		// if it wasn't found in a plugin, fail
		if(!isset($this->_callmap[$method]))
		{
			throw new BadMethodCallException("No plugins with a $method method");
		}

		return call_user_func_array(array(
			$this->_callmap[$method],
			$method
			), $parameters);
	}
}
