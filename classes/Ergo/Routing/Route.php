<?php

namespace Ergo\Routing;

/**
 * A route entry in a {@link Router}.
 *
 * Parses routes with parameters in them e.g
 * <pre>
 * http://example.org/{myparam}
 * http://example.org/{myparam:integer}
 * http://example.org/{myparam:string}
 * http://example.org/{myparam}/{myparam2}?test={myparam3}
 * </pre>
 */
class Route
{
	const REGEX_PARAM = '#{(.+?)(:.+?)?}#';
	const TYPE_ANY = '([^/]+?)';
	const TYPE_STRING = '([\w-_]+)';
	const TYPE_INTEGER = '(\d+)';
	const TYPE_DECIMAL = '([\d]+(?:\.\d+))';
	const TYPE_GREEDY = '(.+?)';

	private $_name;
	private $_template;
	private $_parameters;
	private $_pattern;
	private $_interpolate;

	/**
	 * @param string $name
	 * @param string $template
	 */
	public function __construct($name, $template)
	{
		$this->_name = $name;
		$this->_template = $template;
		$this->_parameters = $this->_extractParameterNames($template);
		$this->_pattern = $this->_getParameterPattern($template);
	}

	/**
	 * @return RouteMatch or null if no match.
	 */
	public function getMatch($path, $metadata=null)
	{
		if($matches = $this->_getMatches($path))
		{
			array_shift($matches);

			$matches = array_map('urldecode', $matches);
			$parameters = empty($matches)
				? array()
				: array_combine($this->_parameters, $matches);

			return new RouteMatch($this->_name, $parameters, $metadata);
		}
		else if(strlen($path) > 1 && substr($path,-1) == '/')
		{
			return $this->getMatch(rtrim($path,'/'), $metadata);
		}

		return null;
	}

	/**
	 * @param array $parameters
	 * @return string
	 */
	public function interpolate($parameters)
	{
		// fail fast if the pattern has a star match
		if(preg_match('/\*/',$this->_pattern))
		{
			throw new BuildException(
				'Can\'t build a url for a pattern with star');
		}

		// pass parameters to callback via private instance variable
		$this->_interpolate = $parameters;

		if (count($diff = array_diff(array_keys($parameters), $this->_parameters)))
		{
			throw new BuildException(sprintf(
				"Unexpected parameter%s [%s] for route '%s'",
				count($diff) == 1 ? '' : 's',
				implode(',', $diff),
				$this->_name
			));
		}

		return preg_replace_callback(
			self::REGEX_PARAM,
			array($this, '_interpolateCallback'),
			$this->_template
		);
	}

	/**
	 * Returns the route name of the entry
	 */
	public function getName()
	{
		return $this->_name;
	}

    /**
     * Returns the names of the replaceable parameters defined in this route
     * @return array
     */
    public function getParameterNames()
    {
        return $this->_parameters;
    }

	// ----------------------------------------

	/**
	* Finds route pattern matches for the given path.
	* Returns empty array if no matches found (indicating route doesn't match $path).
	*
	* @param string $path
	* @return array
	*/
	protected function _getMatches($path)
	{
		preg_match($this->_pattern, $path, $matches);
		return $matches;
	}

	/**
	 * A callback for preg_replace_callback() that returns interpolation parameters
	 */
	private function _interpolateCallback($matches)
	{
		$key = $matches[1];

		if (!isset($this->_interpolate[$key]))
		{
			throw new Exception(sprintf(
				"%s route needs '%s' value for '%s'",
				$this->_name,
				$key,
				$this->_template
			));
		}

		// percent encoding as per section 3.2 of URI Templates draft 9 July 2007,
		// and section 2.1 of http://www.faqs.org/rfcs/rfc3986
		return rawurlencode($this->_interpolate[$key]);
	}

	/**
	 * A callback for preg_replace_callback() that returns pattern chunks for a type
	 */
	private function _typePatternCallback($matches)
	{
		$type = empty($matches[2]) ? 'any' : ltrim($matches[2],':');

		// try basic numeric types
		switch($type)
		{
			case 'any':
				return self::TYPE_ANY;
			case 'int':
			case 'integer':
				return self::TYPE_INTEGER;
			case 'str':
			case 'string':
				return self::TYPE_STRING;
			case 'decimal':
			case 'dec':
				return self::TYPE_DECIMAL;
			case 'greedy':
				return self::TYPE_GREEDY;
		}

		// try the enumeration type
		if(preg_match("#^\(.+?\)$#", $type))
		{
			$items = explode("|",trim($type,"()"));
			$items = array_map('preg_quote', $items);
			return sprintf('(%s)',implode('|',$items));
		}

		// unknown type, fail
		throw new BuildException(
			"Unknown type $type in $this->_template");
	}

	/**
	 * Gets a pattern that can be used for parsing a template
	 * @param string $template
	 * @return array list of
	 */
	private function _getParameterPattern($template)
	{
		// support star matches
		$template = preg_replace('/\*/','.*?',$template);

		return sprintf('#^%s$#',
			preg_replace_callback(
				self::REGEX_PARAM,
				array($this, '_typePatternCallback'),
				$template
			)
		);
	}

	/**
	 * @param string $template
	 * @return array list of parameter names contained in the template.
	 */
	private function _extractParameterNames($template)
	{
		if(!is_string($template))
			throw new \InvalidArgumentException("Template is not a string");

		preg_match_all(self::REGEX_PARAM, $template, $matches);
		return $matches[1];
	}

	/**
	 * Escape for regex except for curly braces, which are pulled out later.
	 * @param string $template
	 * @return string
	 */
	private function _escapeTemplate($template)
	{
		return str_replace(
			array('\{', '\}'),
			array('{', '}'),
			preg_quote($template, '#')
		);
	}
}
