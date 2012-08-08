<?php

namespace Ergo\Http;

/**
 * Encapsulates an HTTP or HTTPS URL parsed from a string.
 *
 * @author Paul Annesley <paul@annesley.cc>
 * @licence http://www.opensource.org/licenses/mit-license.php
 * @see http://github.com/pda/phool
 */
class Url
{

	private $_inputString;
	private $_fragments;

	/**
	 * @param string $urlString
	 */
	public function __construct($urlString)
	{
		$this->_inputString = $urlString;

		if (preg_match('#^//#', $urlString))
		{
			// parse_url treats scheme-relative URLs as path-only...
			$this->_fragments = @parse_url("scheme:$urlString");
			unset($this->_fragments['scheme']);
		}
		else
		{
			$this->_fragments = @parse_url($urlString);
		}
	}

	public static function construct($urlString)
	{
		return new self($urlString);
	}

	/**
	 * Scheme, either http or https
	 * @return string
	 */
	public function getScheme()
	{
		if (!$this->hasScheme()) throw new UrlException(sprintf(
			"URL '%s' has no scheme component",
			$this->_inputString
		));

		return $this->_fragments['scheme'];
	}

	/**
	 * Whether the URL has a scheme component.
	 * @return bool
	 */
	public function hasScheme()
	{
		return !empty($this->_fragments['scheme']);
	}

	/**
	 * Hostname (without port information) e.g. example.org
	 * @return string
	 */
	public function getHost()
	{
		if (!$this->hasHost()) throw new UrlException(sprintf(
			"URL '%s' has no host component",
			$this->_inputString
		));

		return $this->_fragments['host'];
	}

	/**
	 * Whether the URL has a host component.
	 * @return bool
	 */
	public function hasHost()
	{
		return !empty($this->_fragments['host']);
	}

	/**
	 * Host, including port unless it is the default port for the scheme.
	 * @return string
	 */
	public function getHostWithPort()
	{
		if ($this->hasPort() && (!$this->hasDefaultPort() || !$this->isPortDefault()))
		{
			return $this->getHost() . ':' . $this->getPort();
		}
		else
		{
			return $this->getHost();
		}
	}

	/**
	 * Path component (after host, before query string delimiter '?')
	 * @return string
	 */
	public function getPath()
	{
		return empty($this->_fragments['path']) ?
			'/' : $this->_fragments['path'];
	}

	/**
	 * Query string (after query delimiter '?', before fragment delimiter '#')
	 * @return string
	 */
	public function getQueryString()
	{
		if (!$this->hasQueryString()) throw new UrlException(sprintf(
			"URL '%s' has no query string",
			$this->_inputString
		));

		return $this->_fragments['query'];
	}

	/**
	 * Whether the URL has a query string.
	 * @return bool
	 */
	public function hasQueryString()
	{
		return !empty($this->_fragments['query']);
	}

	/**
	 * Fragment string (after fragment delimiter '#')
	 * @return string
	 */
	public function getFragmentString()
	{
		if (!$this->hasFragmentString()) throw new UrlException(sprintf(
			"URL '%s' has no fragment string",
			$this->_inputString
		));

		return $this->_fragments['fragment'];
	}

	/**
	 * Whether the URL has a fragment string.
	 * @return bool
	 */
	public function hasFragmentString()
	{
		return !empty($this->_fragments['fragment']);
	}

	/**
	 * TCP port number, defaults to 80 for HTTP and 443 for HTTPS.
	 * @return int
	 */
	public function getPort()
	{
		return empty($this->_fragments['port']) ?
			$this->getDefaultPort() : $this->_fragments['port'];
	}

	/**
	 * Whether the URL has an explicitly specified port.
	 */
	public function hasPort()
	{
		return isset($this->_fragments['port']) || $this->hasDefaultPort();
	}

	/**
	 * The default TCP port for the scheme of the URL
	 * @return int or null
	 */
	public function getDefaultPort()
	{
		if (!$this->hasDefaultPort()) throw new UrlException(sprintf(
			"No default port for URL '%s'",
			$this->_inputString
		));

		$scheme = $this->getScheme();
		if ($scheme == 'http') return 80;
		elseif ($scheme == 'https') return 443;
		else throw new UrlException("No default port for scheme '$scheme'");
	}

	/**
	 * Whether the URL has a default port (i.e. has a scheme)
	 */
	public function hasDefaultPort()
	{
		return $this->hasScheme();
	}

	/**
	 * Whether the port is the default for the scheme.
	 * @return bool
	 */
	public function isPortDefault()
	{
		return $this->getPort() == $this->getDefaultPort();
	}

	/**
	 * The URL components after the host.
	 * @return string
	 */
	public function getHostRelativeUrl()
	{
		$url = $this->getPath();
		if ($this->hasQueryString()) $url .= '?' . $this->getQueryString();
		if ($this->hasFragmentString()) $url .= '#' . $this->getFragmentString();
		return $url;
	}

	/**
	 * The URL components after the scheme
	 * @return string
	 */
	public function getSchemeRelativeUrl()
	{
		return sprintf(
			'//%s%s',
			$this->getHostWithPort(),
			$this->getHostRelativeUrl()
		);
	}

	/**
	 * Builds a URL with a different path component
	 * @return Url
	 */
	public function getUrlForPath($path)
	{
		$fragments = parse_url($path);

		if (!isset($fragments['path']))
			throw new UrlException("URL is not a valid path: '$path'");

		$newUrl = clone $this;
		$newUrl->_fragments['path'] = $fragments['path'];

		// overwrite RHS components
		foreach(array('query','fragment') as $component)
		{
			if (isset($fragments[$component]))
				$newUrl->_fragments[$component] = $fragments[$component];
			elseif(isset($newUrl->_fragments[$component]))
				unset($newUrl->_fragments[$component]);
		}

		// store the new URL internally
		$newUrl->_inputString = $newUrl->__toString();

		return $newUrl;
	}

	/**
	 * Builds a URL with a different host
	 * @return Url
	 */
	public function getUrlForHost($host)
	{
		$newUrl = clone $this;
		$newUrl->_fragments['host'] = $host;
		$newUrl->_inputString = $newUrl->__toString();

		return $newUrl;
	}

	/**
	 * Builds a URL with a different scheme
	 * @return Url
	 */
	public function getUrlForScheme($scheme)
	{
		$newUrl = clone $this;

		$wasDefaultPort = ($newUrl->hasScheme() && $newUrl->isPortDefault());

		$newUrl->_fragments['scheme'] = $scheme;
		if ($wasDefaultPort)
		{
			$newUrl->_fragments['port'] = $newUrl->getDefaultPort();
		}

		$newUrl->_inputString = $newUrl->__toString();

		return $newUrl;
	}

	/**
	 * Builds a url with a different fragment (the part after # in the url)
	 * @param string
	 * @return Url
	 */
	public function getUrlForFragment($fragment)
	{
		$newUrl = clone $this;
		$newUrl->_fragments['fragment'] = $fragment;
		$newUrl->_inputString = $newUrl->__toString();
		return $newUrl;
	}

	/**
	 * Join path/querystring/fragment components together
	 */
	private function _joinPathComponents($path)
	{
		$result = null;
		foreach(func_get_args() as $p)
		{
			$leading = substr($p,0,1);
			$trailing = substr($result,-1);

			if(!empty($p) && $leading != '/' && $leading != '?' && $trailing != '/')
				$result .= '/';

			$result .= $p;
		}
		return $result;
	}

	/**
	 * Builds a URL with a path component that is relative to the current one
	 * @return Url
	 */
	public function getUrlForRelativePath($path)
	{
		return $this->getUrlForPath(
			$this->_joinPathComponents($this->getPath(),ltrim($path,'/')));
	}

	/**
	 * @param array $queryParameters
	 */
	public function getUrlForParameters($queryParameters)
	{
		$newUrl = clone $this;
		$newUrl->_fragments['query'] = http_build_query($queryParameters);
		$newUrl->_inputString = $newUrl->__toString();

		return $newUrl;
	}

	/**
	 * Builds a URL with added/merged query parameters
	 * @param array $queryParameters
	 */
	public function getUrlForMergedParameters($queryParameters)
	{
		$newUrl = clone $this;

		$querystring = new QueryString($newUrl->_fragments['query']);
		$querystring->addParameters($queryParameters);

		$newUrl->_fragments['query'] = (string)$querystring;
		$newUrl->_inputString = (string)$newUrl;

		return $newUrl;
	}

	/**
	 * @see getUrlForRelativePath($path)
	 */
	public function relative($path)
	{
		return $this->getUrlForRelativePath($path);
	}

	/**
	 * The URL as a string.
	 * @return string
	 */
	public function __toString()
	{
		if ($this->hasScheme())
			return $this->getScheme() . ':' . $this->getSchemeRelativeUrl();

		if ($this->hasHost())
			return $this->getSchemeRelativeUrl();

		return $this->getHostRelativeUrl();
	}
}
