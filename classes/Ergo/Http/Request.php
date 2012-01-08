<?php

namespace Ergo\Http;

/**
 * An HTTP request.
 * @see http://tools.ietf.org/html/rfc2616#section-5
 */
class Request
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
	 * @param Url $url
	 */
	public function __construct($method, $url, $headers=array(), $body=null)
	{
		$this->_method = $method;
		$this->_url = is_string($url) ? new Url($url) : $url;
		$this->_headers = new HeaderCollection($headers);
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
	 * @return Url
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @return QueryString
	 */
	public function getQueryString()
	{
		if(!isset($this->_querystring))
		{
			$this->_querystring = new QueryString(
				$this->getUrl()->hasQueryString() ?
				$this->getUrl()->getQueryString() : ''
				);
		}

		return $this->_querystring;
	}

	/**
	 * @return HeaderCollection
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

		if($headers !== false) $copy->_headers = new HeaderCollection($headers);
		if($body !== false) $copy->_body = $body;
		if($url !== false) $copy->_url = $url;
		if($method !== false) $copy->_method = $method;

		unset($copy->_querystring);

		return $copy;
	}

	/**
	 * @return array
	 */
	public function export()
	{
		return array(
			$this->getRequestMethod(),
			(string)$this->getUrl(),
			$this->getHeaders()->toArray($crlf = false),
			$this->getBody()
		);
	}
}
