<?php

namespace Ergo\Http;

/**
 * Creates a Request from environment data.
 */
class RequestFactory implements \Ergo\SingletonFactory
{
	private $_instance;
	private $_schemaHeader;
	private $_server;

	/**
	 * Constructor
	 * @param array the $_SERVER parameters
	 */
	public function __construct($server)
	{
		$this->_server = $server;
	}

	/**
	 * @return Request
	 */
	public function create()
	{
		if(!isset($this->_instance))
		{
			$this->_instance = new Request(
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
		return new Url(sprintf(
			'%s://%s:%d%s',
			$this->_getScheme(),
			$this->_server['SERVER_NAME'],
			$this->_getPort(),
			$this->_uriRelativeToHost($this->_server['REQUEST_URI'])
		));
	}

	private function _getPort()
	{
		return $this->_getSchemeHeader() == 'https'
			? '443'
			: $this->_server['SERVER_PORT']
			;
	}

	private function _getSchemeHeader()
	{
		if(isset($this->_schemaHeader))
		{
			$header = strtr(sprintf('HTTP_%s',
				strtoupper($this->_schemaHeader)),'-','_');

			return isset($this->_server[$header]) ? $this->_server[$header] : null;
		}
	}

	private function _getScheme()
	{
		if($schemeHeader = $this->_getSchemeHeader())
		{
			return $schemeHeader;
		}
		else
		{
			$requestUrl = new Url($this->_server['REQUEST_URI']);
			return $requestUrl->hasScheme()
				? $requestUrl->getScheme()
				: 'http'
				;
		}
	}

	/**
	 * @return string
	 */
	private function _getRequestMethod()
	{
		return $this->_server['REQUEST_METHOD'];
	}

	/**
	 * @return array
	 */
	private function _getHeaders()
	{
		$headers = array();

		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
				$headers []= new HeaderField($name, $value);
			}
			else if ($name == "CONTENT_TYPE")
			{
				$headers []= new HeaderField('Content-Type', $value);
			}
			else if ($name == "CONTENT_LENGTH")
			{
				$headers []= new HeaderField('Content-Length', $value);
			}
		}

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
		$uri = new Url($uri);
		return $uri->getHostRelativeUrl();
	}
}
