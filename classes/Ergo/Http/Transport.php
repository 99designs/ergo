<?php

namespace Ergo\Http;

class Transport
{
	private $_timeout = 10;
	private $_connectTimeoutMs = 1000;
	private $_proxy;
	private $_auth;

	public function send($request)
	{
		// prepare and send the curl request
		$curl = $this->_curlConnection($request);
		if(($curlResponse = curl_exec($curl)) === false)
		{
			throw new Error('Curl error: ' . curl_error($curl),
				curl_errno($curl));
		}

		$response = $this->_buildResponse($curlResponse);

		curl_close($curl);

		return $response;
	}

	public function setTimeout($seconds)
	{
		$this->_timeout = $seconds;
	}

	public function setConnectTimeoutMs($milliseconds)
	{
		$this->_connectTimeoutMs = $milliseconds;
	}

	public function setHttpProxy($url)
	{
		$this->_proxy = $url;
	}

	public function setHttpAuth($user, $pass)
	{
		$this->_auth = $user . ':' . $pass;
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
		curl_setopt($curl, CURLOPT_TIMEOUT_MS, $this->_timeout * 1000);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $this->_connectTimeoutMs);
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
			$headers[] = HeaderField::fromString($headerline);
		}

		$response = new Response($code,$headers,$body);

		return $response;
	}
}
