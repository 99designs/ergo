<?php

namespace Ergo\Http;

/**
 * The body of an HTTP response.
 */
class ResponseBody
{
	private $_stream, $_string;

	/**
	 * Constructor
	 */
	public function __construct($stream)
	{
		if(is_string($stream))
			$this->_string = $stream;
		else
			$this->_steam = $stream;
	}

	public function __toString()
	{
		return isset($this->_string)
			? $this->_string
			: stream_get_contents($this->_stream)
			;
	}
}

