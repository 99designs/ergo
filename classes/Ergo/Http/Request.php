<?php

/**
 * An HTTP request.
 * @see http://tools.ietf.org/html/rfc2616#section-5
 */
class Ergo_Http_Request
{
	const METHOD_GET = 'GET';
	const METHOD_HEAD = 'HEAD';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';

	private $_method;
	private $_url;
	private $_headers;
	private $_body;
	private $_querystring;

	/**
	 * @param string $method
	 * @param Ergo_Http_Url $url
	 */
	public function __construct($method, $url, $headers, $body=null)
	{
		$this->_method = $method;
		$this->_url = $url;
		$this->_headers = new Ergo_Http_HeaderCollection($headers);
		$this->_body = $body;
	}

	/**
	 * @return string
	 */
	public function getRequestMethod()
	{
		return $this->_method;
	}

	/**
	 * @return Ergo_Http_Url
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @return Ergo_Http_QueryString
	 */
	public function getQueryString()
	{
		if(!isset($this->_querystring))
		{
			$this->_querystring = new Ergo_Http_QueryString(
				$this->getUrl()->hasQueryString() ?
				$this->getUrl()->getQueryString() : ''
				);
		}

		return $this->_querystring;
	}

	/**
	 * @return Ergo_Http_HeaderCollection
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	}

	/**
	 * Creates a new Request object with the parameters specified, or uses
	 * the current object's parameters
	 * @return object
	 */
	public function copy($headers=false, $body=false, $url=false, $method=false)
	{
		$copy = clone $this;

		if($headers !== false) $copy->_headers = new Ergo_Http_HeaderCollection($headers);
		if($body !== false) $copy->_body = $body;
		if($url !== false) $copy->_url = $url;
		if($method !== false) $copy->_method = $method;

		unset($copy->_querystring);

		return $copy;
	}
}
