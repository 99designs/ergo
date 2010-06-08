<?php

/**
 * Creates a Ergo_Http_Request from environment data.
 */
class Ergo_Http_RequestFactory implements Ergo_SingletonFactory
{
	private $_instance;
	private $_schemaHeader;

	/**
	 * @return Ergo_Http_Request
	 */
	public function create()
	{
		if(!isset($this->_instance))
		{
			$this->_instance = new Ergo_Http_Request(
				$this->_getRequestMethod(),
				$this->_getUrl(),
				$this->_getHeaders(),
				$this->_getBody()
			);
		}

		return $this->_instance;
	}

	/**
	 * Sets the HTTP header to examine to determine the scheme, either http
	 * or https. E.g X-Forwarded-Proto
	 * @chainable
	 */
	public function setSchemeHeader($header)
	{
		$this->_schemaHeader = $header;
		return $this;
	}

	/* (non-phpdoc)
	 * @return
	 */
	public function clear()
	{
		unset($this->_instance);
		return $this;
	}

	// ----------------------------------------

	private function _getUrl()
	{
		return new Ergo_Http_Url(sprintf(
			'%s://%s:%d%s',
			$this->_getScheme(),
			$_SERVER['HTTP_HOST'],
			$this->_getPort(),
			$this->_uriRelativeToHost($_SERVER['REQUEST_URI'])
		));
	}

	private function _getPort()
	{
		return $this->_getSchemeHeader() == 'https'
			? '443'
			: $_SERVER['SERVER_PORT']
			;
	}

	private function _getSchemeHeader()
	{
		if(isset($this->_schemaHeader))
		{
			$header = strtr(sprintf('HTTP_%s',
				strtoupper($this->_schemaHeader)),'-','_');

			return isset($_SERVER[$header]) ? $_SERVER[$header] : null;
		}
	}

	private function _getScheme()
	{
		if(isset($this->_schemaHeader))
		{
			$header = strtr(sprintf('HTTP_%s',
				strtoupper($this->_schemaHeader)),'-','_');

			return (isset($_SERVER[$header]) && $_SERVER[$header] == 'https')
				? 'https' : 'http';
		}
		else
		{
			return 'http';
		}
	}

	/**
	 * @return string
	 */
	private function _getRequestMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * @return array
	 */
	private function _getHeaders()
	{
		if (!function_exists('apache_request_headers')) return array();

		foreach (apache_request_headers() as $name => $value)
			$headers []= new Ergo_Http_HeaderField($name, $value);

		return $headers;
	}

	/**
	 * @return string
	 */
	private function _getBody()
	{
		return file_get_contents('php://input');
	}

	/**
	 * The path of the URI, which may or may not already be path-only.
	 * @param string $uri
	 * @return string
	 */
	private function _uriRelativeToHost($uri)
	{
		$uri = new Ergo_Http_Url($uri);
		return $uri->getHostRelativeUrl();
	}
}
