<?php

namespace Ergo\Http;

/**
 * An HTTP response.
 * @see http://tools.ietf.org/html/rfc2616#section-6
 */
class Response
{
	private $_status;
	private $_headers;
	private $_body;

	/**
	 * @param Status $status
	 * @param HeaderField[] $headers
	 * @param string $body
	 */
	public function __construct($status, $headers, $body=null)
	{
		$this->_status = is_numeric($status) ? new Status($status) : $status;
		$this->_headers = is_object($headers) ? $headers : new HeaderCollection($headers);
		$this->_body = is_string($body) && !empty($body) ? new ResponseBody($body) : $body;
	}

	/**
	 * @return Status
	 */
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * @return HeaderCollection
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
