<?php

/**
 * Sends an HTTP response using PHP methods.
 * @see http://tools.ietf.org/html/rfc2616#section-6
 */
class Ergo_Http_ResponseSender
{
	private $_response;

	/**
	 * @param Ergo_Http_Response $response
	 */
	public function __construct($response)
	{
		$this->_response = $response;
	}

	/**
	 * Sends the headers and body.
	 */
	public function send()
	{
		$response = $this->_response;
		header($response->getStatus()->__toString());

		foreach ($response->getHeaders() as $headerField)
			header($headerField);

		if ($response->hasBody()) echo $response->getBody();
	}

}
