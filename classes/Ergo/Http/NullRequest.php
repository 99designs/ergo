<?php

namespace Ergo\Http;

/**
 * An HTTP request created without an actual request.
 * @see http://tools.ietf.org/html/rfc2616#section-5
 */
class NullRequest
{
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
	}

	/**
	 * @return string
	 */
	public function getRequestMethod()
	{
    return NULL;
	}

	/**
	 * @return Url
	 */
	public function getUrl()
	{
    return NULL;
	}

	/**
	 * @return QueryString
	 */
	public function getQueryString()
	{
		return new QueryString('');
	}

	/**
	 * @return HeaderCollection
	 */
	public function getHeaders()
	{
		return new HeaderCollection(array());
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
    return NULL;
	}

	/**
	 * Creates a new Request object with the parameters specified, or uses
	 * the current object's parameters
	 * @return object
	 */
	public function copy($headers=false, $body=false, $url=false, $method=false)
	{
    return clone $this;
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


