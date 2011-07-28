<?php

namespace Ergo\Http;

/**
 * Builds up data for, and then creates, a Response
 */
class ResponseBuilder
{
	private $_status;
	private $_headerFields = array();
	private $_body = '';
	private $_view;
	private $_cacheControl;

	/**
	 * @param int $code
	 * @chainable
	 */
	public function setStatusCode($code)
	{
		$this->_status = new Status($code);
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @chainable
	 */
	public function addHeader($name, $value)
	{
		$this->_headerFields []= new HeaderField($name, $value);
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
	 * Uses a view to populate the body of the response
	 * @param string $view
	 * @chainable
	 */
	public function view(\Ergo\View $view)
	{
		$this->_view = $view;
		return $this;
	}

	/**
	 * @return Response
	 */
	public function build()
	{
		if (isset($this->_view)) $this->setBody($this->_view->output());

		if (!$this->_status) $this->setStatusCode(200);

		if (isset($this->_location))
			$this->addHeader('Location', $this->_location);

		if(isset($this->_cacheControl))
			$this->addHeader('Cache-Control',
				implode(', ', $this->_cacheControl));

		$this->addHeader('Content-Length', strlen($this->_body));

		return new Response(
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

	/**
	 * Sets the Cache-Control header to the strings passed as parameters.
	 * @chainable
	 */
	public function cacheControl(/* ... */)
	{
		$this->_cacheControl = func_get_args();
		return $this;
	}

	/**
	 * Sets the Expires header based on a Unix timestamp.
	 * @param int Unix timestamp
	 * @chainable
	 */
	public function expires($time)
	{
		if(!empty($time) && is_string($time) && !$timestamp = strtotime($time))
		{
			throw new \Ergo\Routing\Exception("Invalid expiry time: $timestamp");
		}
		else if(is_numeric($time))
		{
			$timestamp = $time;
		}
		else if($time == false)
		{
			return $this;
		}

		$this->addHeader('Expires', date('r',$timestamp));

		return $this;
	}

	/**
	 * Sets the headers required to prevent response caching.
	 * @chainable
	 */
	public function notCachable()
	{
		return $this
			->cacheControl('no-store', 'no-cache', 'must-revalidate')
			->addHeader('Pragma', 'no-cache')
			->expires(strtotime('-1 year'));
	}
}
