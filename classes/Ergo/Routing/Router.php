<?php

namespace Ergo\Routing;

/**
 * A router connects requests to controllers via route patterns
 */
class Router implements Controller
{
	private $_routes = array();
	private $_controllers = array();
	private $_resolver;
	private $_prefixes = array();

	/**
	 * Constructor
	 * @param ControllerResolver
	 */
	public function __construct($resolver=null)
	{
		$this->_resolver = $resolver;

		$this
			->prefix('redirect', function($str){ return new RedirectController($str); })
			->prefix('alias', function($str, $router){ return $router->controller($str); })
			;
	}

	/**
	 * Connects a route template to
	 * @param string a url template
	 * @param string a unique name for the route
	 * @chainable
	 */
	public function connect($template, $name, $controller=null)
	{
		$this->_routes[$name] = new Route($name, $template);

		if($controller)
			$this->_controllers[$name] = $controller;

		return $this;
	}

	/**
	 * Looks up a route match based on a url path
	 * @param string $path
	 * @return RouteMatch
	 */
	public function lookup($path)
	{
		foreach ($this->_routes as $route)
		{
			if ($match = $route->getMatch($path))
				return $match;
		}

		throw new LookupException("No route matches path '$path'");
	}

	/**
	 * Looks up a route by name
	 * @return Route
	 */
	public function routeByName($name)
	{
		if(!isset($this->_routes[$name]))
			throw new LookupException("No route named '$name'");

		return $this->_routes[$name];
	}

	/**
	 * Build a URL path based on a route name and associated parameters.
	 * @param string $name
	 * @param array $parameters
	 */
	public function buildUrl($name, $parameters = array())
	{
		return $this->routeByName($name)->interpolate($parameters);
	}

	/**
	 * Looks up a controller from a route name
	 */
	public function controller($name)
	{
		if(isset($this->_controllers[$name]))
		{
			$controller = $this->_controllers[$name];

			if(is_string($controller))
				return $this->_controllerFromString($controller);
			else if(is_callable($controller))
				return new CallbackController($controller);
			else if(is_object($controller))
				return $controller;
		}
		else if($this->_resolver)
		{
			return $this->_resolver->resolve($name);
		}

		throw new LookupException("No controller defined for route '$name'");
	}

	/**
	 * Register a callback for parsing a controller prefix.
	 * @chainable
	 */
	public function prefix($prefix, $callback)
	{
		$this->_prefixes[$prefix] = $callback;
		return $this;
	}

	/**
	 * @return Controller
	 */
	private function _controllerFromString($string)
	{
		if(preg_match('/^(.+?)\:(.+?)$/', $string, $m))
		{
			if(isset($this->_prefixes[$m[1]]))
				return call_user_func($this->_prefixes[$m[1]], $m[2], $this);
		}

		throw new LookupException("Unknown controller string format '$string'");
	}

	/* (non-phpdoc)
	 * @see Controller::execute()
	 */
	public function execute($request)
	{
		$match = $this->lookup($request->getUrl()->getPath());
		$controller = $this->controller($match->getName());
		return $controller->execute(new RoutedRequest($request, $match, $this));
	}
}
