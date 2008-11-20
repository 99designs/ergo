<?php

/**
 * Creates a Ergo_Http_Request from environment data.
 */
class Ergo_Http_RequestFactory implements Ergo_Factory
{

	/**
	 * @return Ergo_Http_Request
	 */
	public function create()
	{
		return new Ergo_Http_Request(
			$this->_getRequestMethod(),
			$this->_getUrl(),
			$this->_getHeaders(),
			$this->_getBody()
		);
	}

	// ----------------------------------------

	private function _getUrl()
	{
		return new Ergo_Http_Url(sprintf(
			'http://%s:%d%s',
			$_SERVER['HTTP_HOST'],
			$_SERVER['SERVER_PORT'],
			$_SERVER['REQUEST_URI']
		));
	}

	/**
	 * @return string
	 */
	private function _getRequestMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * @return array
	 */
	private function _getHeaders()
	{
		if (!function_exists('apache_request_headers')) return array();

		foreach (apache_request_headers() as $name => $value)
			$headers []= new Ergo_Http_HeaderField($name, $value);

		return $headers;
	}

	/**
	 * @return string
	 */
	private function _getBody()
	{
		return file_get_contents('php://input');
	}

}

?>
