<?php

/**
 * An HTTP status, with a code and a message.
 */
class Ergo_Http_Status
{
	/**
	 * @var array $_messageMap
	 * @see {@link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes}
	 */
	private $_messageMap = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		102 => 'Processing',
		207 => 'Multi-Status',
		418 => 'I\'m a Teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		449 => 'Retry With',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
	);

	/**
	 * @param int
	 */
	public function __construct($statusCode)
	{
		$this->_statusCode = $statusCode;
	}

	/**
	 * @return int
	 */
	public function getCode()
	{
		return $this->_statusCode;
	}

	/**
	 * The message associated with, but not including, the status code.
	 * @return string
	 */
	public function getMessage()
	{
		$code = $this->getCode();

		if (!array_key_exists($code, $this->_messageMap))
			throw new Ergo_Routing_Exception("Unknown HTTP status code: $code");

		return $this->_messageMap[$code];
	}

	/**
	 * The full status line.
	 * @return string
	 */
	public function getStatusLine()
	{
		return sprintf(
			"HTTP/1.1 %d %s\r\n",
			$this->getCode(),
			$this->getMessage()
		);
	}

	public function __toString()
	{
		return $this->getStatusLine();
	}

	public function isOk()
	{
		return $this->getCode() == 200;
	}
}
