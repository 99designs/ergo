<?php

namespace Ergo\Http;

use Ergo\Http\Error;

/**
 * A simple HTTP client
 */
class Client
{
	const MAX_REDIRECTS=10;

	private $_url;
	private $_redirects=0;
	private $_filters=array();
	private $_headers=array();


	public static $requestCount=0;
	public static $requestTime=0;

	private static $_transport;

	/**
	 * @param string $url
	 */
	function __construct($url)
	{
		if (!$url) throw new \InvalidArgumentException('A base url must be set');
		$this->_url = new Url($url);
	}

	public static function transport($transport=null)
	{
		if (!is_null($transport))
			self::$_transport = $transport;

		if (!isset(self::$_transport))
			self::$_transport = new Transport();

		return self::$_transport;
	}

	/**
	 * Adds an HTTP header to all requests
	 * @chainable
	 */
	public function addFilter(ClientFilter $filter)
	{
		$this->_filters[] = $filter;
		return $this;
	}

	/**
	 * Adds an HTTP header to all requests
	 * @param mixed either a string or a HeaderField
	 * @chainable
	 */
	public function addHeader($header)
	{
		if(is_string($header)) $header = HeaderField::fromString($header);
		$this->_headers[] = $header;
		return $this;
	}

	/**
	 * Sets an HTTP proxy to use
	 */
	public function setHttpProxy($url)
	{
		self::transport()->setHttpProxy($url);
		return $this;
	}

	/**
	 *	Sets HTTP authentication credentials
	 */
	public function setHttpAuth($user, $pass)
	{
		self::transport()->setHttpAuth($user, $pass);
		return $this;
	}

	/**
	 * Sets the connection timeout in seconds
	 * @chainable
	 */
	public function setTimeout($seconds)
	{
		self::transport()->setTimeout($seconds);
		return $this;
	}

	/**
	 * Sends a POST request
	 * @return Response
	 */
	function post($path, $body, $contentType = null)
	{
		return $this->_dispatchRequest(
			$this->_buildRequest('POST', $path, $body, $contentType)
		);
	}

	/**
	 * Sends a PUT request
	 * @return Response
	 */
	function put($path, $body, $contentType = null)
	{
		return $this->_dispatchRequest(
			$this->_buildRequest('PUT', $path, $body, $contentType)
		);
	}

	/**
	 * Sends a GET request
	 * @return Response
	 */
	function get($path)
	{
		return $this->_dispatchRequest(
			$this->_buildRequest('GET', $path)
		);
	}

	/**
	 * Sends a DELETE request
	 * @return Response
	 */
	function delete($path)
	{
		return $this->_dispatchRequest(
			$this->_buildRequest('DELETE',$path)
		);
	}

	/**
	 * Builds an Request object
	 */
	private function _buildRequest($method, $path, $body = null, $contentType = null)
	{
		// copy default headers
		$headers = $this->_headers;

		// add Content-Type header if provided
		if ($contentType)
			$headers []= new HeaderField('Content-Type', $contentType);

		$request = new Request(
			$method,
			$this->_url->getUrlForRelativePath($path),
			$headers,
			$body
		);

		// pass the request through the filter chain
		foreach($this->_filters as $filter)
		{
			$request = $filter->request($request);
		}

		return $request;
	}

	/**
	 * Dispatches a request via CURL
	 */
	private function _dispatchRequest($request)
	{
		// track the number of requests across instances
		self::$requestCount++;
		$timestart = microtime(true);

		$response = self::transport()->send($request);

		// pass the response through the filter chain
		foreach($this->_filters as $filter)
		{
			$response = $filter->response($response);
		}

		$httpCode = $response->getStatus()->getCode();
		$location = $response->getHeaders()->value('Location');
		$body = $response->getBody();

		// track the time taken across instances
		self::$requestTime += microtime(true) - $timestart;

		// process a redirect if needed
		if($httpCode < 400 && $location)
		{
			return $this->_redirect($location);
		}
		else
		{
			$this->_redirects = 0;
		}

		// translate error code to a typed exception
		if($httpCode == 500)
		{
			throw new Error\InternalServerError($body);
		}
		elseif($httpCode == 400)
		{
			throw new Error\BadRequest($body);
		}
		elseif($httpCode == 401)
		{
			throw new Error\Unauthorized($body);
		}
		elseif($httpCode == 404)
		{
			throw new Error\NotFound($body);
		}
		else if($httpCode >= 300)
		{
			throw new Error($body,$httpCode);
		}

		return $response;
	}

	/**
	 * Redirect to a new url
	 */
	private function _redirect($location)
	{
		$locationUrl = new Url($location);

		// if the location header was relative (bleh) add the host
		if(!$locationUrl->hasHost())
		{
			$locationUrl = $this->_url->getUrlForPath($location);
		}

		if($this->_redirects > self::MAX_REDIRECTS)
		{
			throw new Error\BadRequest("Exceeded maximum redirects");
		}

		$this->_redirects++;

		return $this->_dispatchRequest(
			new Request('GET', $locationUrl, $this->_headers));
	}
}
