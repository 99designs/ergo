<?php

/**
 * An HTTP response.
 * @see http://tools.ietf.org/html/rfc2616#section-6
 */
class Ergo_Http_Response
{
	private $_status;
	private $_headers;
	private $_body;

	/**
	 * @param Ergo_Http_Status $status
	 * @param Ergo_Http_HeaderField[] $headers
	 * @param string $body
	 */
	public function __construct($status, $headers, $body=null)
	{
		if (is_numeric($status)) $status = new Ergo_Http_Status($status);
		$this->_status = $status;
		$this->_headers = new Ergo_Http_HeaderCollection($headers);
		$this->_body = $body;
	}

	/**
	 * @return Ergo_Http_Status
	 */
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * @return Ergo_Http_HeaderCollection
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * @return bool
	 */
	public function hasBody()
	{
		return (bool)$this->_body;
	}

	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	}

}
