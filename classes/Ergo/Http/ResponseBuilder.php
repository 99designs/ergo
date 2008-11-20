<?php

/**
 * Builds up data for, and then creates, a Ergo_Http_Response
 */
class Ergo_Http_ResponseBuilder
{
	private $_status;
	private $_headerFields = array();
	private $_body = '';
	private $_cacheControl;

	/**
	 * @param int $code
	 * @chainable
	 */
	public function setStatusCode($code)
	{
		$this->_status = new Ergo_Http_Status($code);
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @chainable
	 */
	public function addHeader($name, $value)
	{
		$this->_headerFields []= new Ergo_Http_HeaderField($name, $value);
		return $this;
	}

	/**
	 * Sets the body of the response.
	 * @param string $body
	 * @chainable
	 */
	public function setBody($body)
	{
		$this->_body = $body;
		return $this;
	}

	/**
	 * @return Ergo_Http_Response
	 */
	public function build()
	{
		if (!$this->_status) $this->setStatusCode(200);

		if (isset($this->_location))
			$this->addHeader('Location', $this->_location);

		if(isset($this->_cacheControl))
			$this->addHeader('Cache-Control',
				implode(', ', $this->_cacheControl));

		$this->addHeader('Content-Length', strlen($this->_body));

		return new Ergo_Http_Response(
			$this->_status,
			$this->_headerFields,
			$this->_body
		);
	}

	// ----------------------------------------
	// convenience methods: common responses

	/**
	 * Configures response as a '403 Forbidden'
	 * @chainable
	 */
	public function forbidden()
	{
		return $this->setStatusCode(403);
	}

	/**
	 * Configures response as a '404 Not Found'
	 * @chainable
	 */
	public function notFound()
	{
		return $this->setStatusCode(404);
	}

	/**
	 * Configures response as a '405 Method Not Allowed'
	 * @chainable
	 */
	public function methodNotAllowed()
	{
		return $this->setStatusCode(405);
	}

	/**
	 * Configures response as a '302 Found' temporary redirect.
	 * @param string $location
	 * @chainable
	 */
	public function found($location)
	{
		$this->_location = $location;
		return $this->setStatusCode(302);
	}

	/**
	 * Configures response as a '301 Moved Permanently' permanent redirect.
	 * @param string $location
	 * @chainable
	 */
	public function moved($location)
	{
		$this->_location = $location;
		return $this->setStatusCode(301);
	}

	/**
	 * Configures response as a '201 Created'.
	 * @param string $location
	 * @chainable
	 */
	public function created($location)
	{
		$this->_location = $location;
		return $this->setStatusCode(201);
	}

	/**
	 * Configures response as a '304 Not Modified'
	 * @chainable
	 */
	public function notModified()
	{
		return $this->setStatusCode(304);
	}

	// ----------------------------------------
	// convenience methods: cacheability

	public function cacheControl($params)
	{
		$this->_cacheControl = func_get_args();
	}

	public function expires($time)
	{
		if(!empty($time) && is_string($time) && !$timestamp = strtotime($time))
		{
			throw new Ergo_Routing_Exception("Invalid expiry time: $timestamp");
		}
		else if(is_numeric($time))
		{
			$timestamp = $time;
		}
		else if($time == false)
		{
			return $this;
		}

		return $this->addHeader('Expires', date('r',$timestamp));
	}

}
