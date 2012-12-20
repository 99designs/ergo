<?php

namespace Ergo\Routing;

/**
 * A router connects requests to controllers via route patterns
 */
class Router implements Controller
{
	private $_routes = array();
	private $_customRoutes = array();
	private $_controllers = array();
	private $_defaultMetadata = array();
	private $_metadata = array();
	private $_resolver;
	private $_baseUrl;

	/**
	 * Constructor
	 * @param ControllerResolver
	 */
	public function __construct($resolver=null)
	{
		$this->_resolver = $resolver;
	}

	/**
	 * Sets a base url for url building
	 * @chainable
	 */
	public function setBaseUrl($url)
	{
		$this->_baseUrl = $url;
		return $this;
	}

	/**
	 * Sets the default metadata to be set into routes
	 */
	public function setDefaultMetadata($metadata)
	{
		$this->_defaultMetadata = $metadata;
		return $this;
	}

	/**
	 * Configures the routemap from a php file
	 * @chainable
	 */
	public function configure($file)
	{
		require($file);
		return $this;
	}

	/**
	 * Gives a route template a unique name and a controller to route to
	 * @param string a url template
	 * @param string a unique name for the route
	 * @param mixed a controller, either a class, a string or a callback
	 * @chainable
	 */
	public function connect($template, $name, $controller=null, $metadata=array())
	{
		if(is_object($template))
			$this->_customRoutes[$name] = $template;
		else
			$this->_routes[$name] = new Route($name, $template);

		if($metadata)
			$this->_metadata[$name] = $metadata;

		if($controller)
			$this->_controllers[$name] = $controller;

		return $this;
	}

	/**
	 * Create a redirect from a particular route name to another
	 * @chainable
	 */
	public function redirect($template, $name, $to)
	{
		return $this->connect($template, $name, new RedirectController($to));
	}

	/**
	 * Register an alias from one route name to another
	 */
	public function alias($template, $name, $to)
	{
		$router = $this;
		return $this->connect($template, $name, function($request) use($router, $to) {
			return $router->controller($to)->execute($request);
		});
	}

	/**
	 * Looks up a route match based on a url path
	 * @param string $path
	 * @return RouteMatch
	 */
	public function lookup($path)
	{
		if($match = $this->_getRouteMatch($path,$this->_routes))
			return $match;

		if($match = $this->_getRouteMatch($path,$this->_customRoutes))
			return $match;

		throw new LookupException("No route matches path '$path'");
	}

	/**
	 * Looks up metadata for a route name
	 * @return array
	 */
	public function metadata($routeName)
	{
		return isset($this->_metadata[$routeName])
			? array_merge($this->_defaultMetadata, $this->_metadata[$routeName])
			: $this->_defaultMetadata
			;
	}

	/**
	 * Looks up a route by name
	 * @return Route
	 */
	public function routeByName($name)
	{
		if (isset($this->_routes[$name]))
				return $this->_routes[$name];

		if (isset($this->_customRoutes[$name]))
				return $this->_customRoutes[$name];

		throw new LookupException("No route named '$name'");
	}

	/**
	 * Build a URL path based on a route name and associated parameters.
	 * @param string $name
	 * @param array $parameters
	 */
	public function buildUrl($name, $parameters=array(), $baseUrl=null)
	{
		$url = $this->routeByName($name)->interpolate($parameters);

		if($baseUrl)
			return (string) $baseUrl->relative($url);
		else if(isset($this->_baseUrl))
			return $this->_baseUrl->relative($url);
		else
			return $url;
	}

	/**
	 * Looks up a controller from a route name
	 */
	public function controller($name)
	{
		if(isset($this->_controllers[$name]) && ($controller = $this->_controllers[$name]))
		{
			return is_callable($controller)
				? new CallbackController($controller)
				: $controller
				;
		}

		if($this->_resolver)
			return $this->_resolver->resolve($name);

		throw new LookupException("No controller defined for route '$name'");
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

	/**
	 * Look for a matching route in provided route list.
	 * @param string $path
	 * @param array $routes
	 * @return mixed Ergo\Routing\Route or boolean
	 */
	private function _getRouteMatch($path,$routes)
	{
		foreach ($routes as $route)
		{
			if ($match = $route->getMatch($path, $this->metadata($route->getName())))
				return $match;
		}
		return false;
	}
}
