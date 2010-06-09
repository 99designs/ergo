<?php

/**
 * A simple HTTP client
 */
class Ergo_Http_Client
{
	const MAX_REDIRECTS=10;
	const DEFAULT_TIMEOUT=10;

	private $_url;
	private $_redirects=0;
	private $_filters=array();
	private $_headers=array();
	private $_proxy;
	private $_auth;
	private $_timeout=self::DEFAULT_TIMEOUT;

	public static $requestCount=0;

	/**
	 * @param string $url
	 */
	function __construct($url)
	{
		if (!$url) throw new InvalidArgumentException('A base url must be set');
		$this->_url = new Ergo_Http_Url($url);
	}

	/**
	 * Adds an HTTP header to all requests
	 * @chainable
	 */
	public function addFilter(Ergo_Http_ClientFilter $filter)
	{
		$this->_filters[] = $filter;
		return $this;
	}

	/**
	 * Adds an HTTP header to all requests
	 * @param mixed either a string or a Ergo_Http_HeaderField
	 * @chainable
	 */
	public function addHeader($header)
	{
		if(is_string($header)) $header = Ergo_Http_HeaderField::fromString($header);
		$this->_headers[] = $header;
		return $this;
	}

	/**
	 * Sets an HTTP proxy to use
	 */
	public function setHttpProxy($url)
	{
		$this->_proxy = $url;
		return $this;
	}
	
	/**
	 *	Sets HTTP authentication credentials
	 */
	public function setHttpAuth($user, $pass)
	{
		$this->_auth = $user . ':' . $pass;
		return $this;
	}

	/**
	 * Sends a POST request
	 * @return Ergo_Http_Response
	 */
	function post($path, $body)
	{
		return $this->_dispatchRequest($this->_buildRequest('POST',$path,$body));
	}

	/**
	 * Sends a PUT request
	 * @return Ergo_Http_Response
	 */
	function put($path, $body)
	{
		return $this->_dispatchRequest($this->_buildRequest('PUT',$path,$body));
	}

	/**
	 * Sends a GET request
	 * @return Ergo_Http_Response
	 */
	function get($path)
	{
		return $this->_dispatchRequest($this->_buildRequest('GET',$path));
	}

	/**
	 * Sends a DELETE request
	 * @return Ergo_Http_Response
	 */
	function delete($path)
	{
		return $this->_dispatchRequest($this->_buildRequest('DELETE',$path));
	}

	/**
	 * Parses a response into headers and a body
	 */
	private function _buildResponse($response)
	{		
		$sections = explode("\r\n\r\n", $response,2);
		$body = isset($sections[1]) ? $sections[1] : NULL;
		$headers = array();
		$headerlines = explode("\n",$sections[0]);

		// process status
		list($http, $code, $message) = explode(' ',$headerlines[0],3);

		// process headers
		foreach(array_slice($headerlines,1) as $headerline)
		{
			$headers[] = Ergo_Http_HeaderField::fromString($headerline);
		}

		$response = new Ergo_Http_Response($code,$headers,$body);

		// pass the response through the filter chain
		foreach($this->_filters as $filter)
		{
			$response = $filter->response($response);
		}

		return $response;
	}

	/**
	 * Builds an Ergo_Http_Request object
	 */
	private function _buildRequest($method,$path,$body=null)
	{
		$request = new Ergo_Http_Request(
			$method, $this->_url->getUrlForRelativePath($path),
			$this->_headers, $body);

		return $request;
	}

	/**
	 * Dispatches a request via CURL
	 */
	private function _dispatchRequest($request)
	{
		// pass the request through the filter chain
		foreach($this->_filters as $filter)
		{
			$request = $filter->request($request);
		}

		// track the number of requests across instances
		self::$requestCount++;

		// prepare and send the curl request
		$curl = $this->_curlConnection($request);
		if(($curlResponse = curl_exec($curl)) === false)
		{
			throw new Ergo_Http_Error('Curl error: ' . curl_error($curl),
				curl_errno($curl));
		}

		$response = $this->_buildResponse($curlResponse);
		$httpCode = $response->getStatus()->getCode();
		$location = $response->getHeaders()->value('Location');
		$body = $response->getBody();

		curl_close($curl);

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
			throw new Ergo_Http_Error_InternalServerError($body);
		}
		elseif($httpCode == 400)
		{
			throw new Ergo_Http_Error_BadRequest($body);
		}
		elseif($httpCode == 401)
		{
			throw new Ergo_Http_Error_Unauthorized($body);
		}
		elseif($httpCode == 404)
		{
			throw new Ergo_Http_Error_NotFound($body);
		}
		else if($httpCode >= 300)
		{
			throw new Ergo_Http_Error($body,$httpCode);
		}

		return $response;
	}

	/**
	 * Initializes the curl connection
	 */
	private function _curlConnection($request)
	{
		// create a new curl resource
		$curl = curl_init();
		$method = $request->getRequestMethod();
		$headers = array('Expect:');

		// add existing headers into a flat string format
		foreach($request->getHeaders() as $header)
		{
			$headers[] = rtrim($header->__toString());
		}

		// set URL and other appropriate options
		curl_setopt($curl, CURLOPT_URL, $request->getUrl());
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

		// enable proxy support
		if(isset($this->_proxy))
		{
			curl_setopt($curl, CURLOPT_PROXY, $this->_proxy);
		}
		
		// enable http authentication
		if(isset($this->_auth))
		{
			curl_setopt($curl, CURLOPT_USERPWD, $this->_auth);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}

		if($method == 'PUT' || $method == 'POST')
		{
			$headers[] = 'Content-Length: '.strlen($request->getBody());

			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
		}
		elseif($method == 'DELETE')
		{
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		}

		// add HTTP headers
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		return $curl;
	}

	/**
	 * Redirect to a new url
	 */
	private function _redirect($location)
	{
		$locationUrl = new Ergo_Http_Url($location);

		// if the location header was relative (bleh) add the host
		if(!$locationUrl->hasHost())
		{
			$locationUrl = $this->_url->getUrlForPath($location);
		}

		if($this->_redirects > self::MAX_REDIRECTS)
		{
			throw new Ergo_Http_Error_BadRequest("Exceeded maximum redirects");
		}

		$this->_redirects++;

		return $this->_dispatchRequest(
			new Ergo_Http_Request('GET', $locationUrl, $this->_headers));
	}

	/**
	 * Sets the connection timeout in seconds
	 * @chainable
	 */
	public function setTimeout($seconds)
	{
		$this->_timeout = $seconds;
		return $this;
	}
}
