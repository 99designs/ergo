<?php

/**
 * Maps URL templates to named routes.
 */
class Ergo_Routing_RouteMap
{
	private $_entries = array();

	/**
	 * Defines url=>routename mappings in bulk
	 * @param array
	 */
	public function map($routes)
	{
		foreach($routes as $url=>$name)
		{
			if(isset($this->_entries[$name]))
			{
				throw new Ergo_Routing_BuildException(
					"A route named $name exists already");
			}

			$this->_entries[$name] =
				new Ergo_Routing_RouteMapEntry($name, $url);
		}
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
