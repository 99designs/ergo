<?php

/**
 * Maps URL templates to named routes.
 * Supports url templates.
 */
class Ergo_Routing_RouteMap
{
	private $_entries = array();

	/**
	 * Maps a url template to a named route, along with optional tags
	 * @param string a url template
	 * @param string a unique name for the route
	 * @chainable
	 */
	public function map($template, $routeName, $tags=array())
	{
		if(isset($this->_entries[$routeName]))
		{
			throw new Ergo_Routing_BuildException(
				"A route named $routeName exists already");
		}

		$this->_entries[$routeName] =
			new Ergo_Routing_RouteMapEntry($routeName, $template, $tags);

		return $this;
	}

	/**
	 * Look up a RouteMapMatch based on the path of a URL.
	 * @param string $path
	 */
	public function lookup($path)
	{
		foreach ($this->_entries as $entry)
		{
			if ($match = $entry->getMatch($path))
			{
				return $match;
			}
		}

		throw new Ergo_Routing_LookupException("No route matches path '$path'");
	}

	/**
	 * Build a URL path based on a route name and associated parameters.
	 * @param string $name
	 * @param array $parameters
	 */
	public function buildUrl($name, $parameters = array())
	{
		if(!isset($this->_entries[$name]))
		{
			throw new Ergo_Routing_BuildException("No route named '$name'");
		}

		return $this->_entries[$name]->interpolate($parameters);
	}
}
