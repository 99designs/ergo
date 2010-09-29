<?php

/**
 * A handler that displays an error in a web-friendly way
 */
class Ergo_Error_WebErrorHandler extends Ergo_Error_AbstractErrorHandler
{
	const EXIT_CODE = 2;

	/* (non-phpdoc)
	 * @see Ergo_Error_ErrorHandler::context()
	 */
	public function context()
	{
		$request = Ergo::request();
		$headers = $request->getHeaders();

		$hostname = $headers->value('Host');
		$requestStr = sprintf('%s %s',
			$request->getRequestMethod(),
			$request->getUrl()
		);

		// add some metadata
		return array(
			'Environment'=>'Web',
			'Request' => $requestStr,
			'Server Name' => $this->_server('SERVER_NAME', 'unknown'),
			'Host' => $hostname,
			'Referer' => $this->_server('HTTP_REFERER', 'unknown'),
			'User IP' => $this->_server('REMOTE_ADDR', 'unknown'),
			'User Agent' => $this->_server('HTTP_USER_AGENT', 'unknown'),
		);
	}

	/**
	 * Builds a response object
	 */
	protected function buildResponseBody($e)
	{
		$context = '';
		foreach($this->context() as $key=>$value)
		{
			$context .= "$key: $value\n";
		}

		return sprintf(
			'<h1>Error: %s</h1><pre>%s</pre><h2>Context</h2><pre>%s</pre>',
			$e->getMessage(),
			$e->__toString(),
			$context);
	}
	/**
	 * Builds a response object
	 */
	protected function buildResponse($e)
	{
		// build a response
		$responseBuilder = new Ergo_Http_ResponseBuilder();
		return $responseBuilder
			->setStatusCode(500)
			->notCachable()
			->setBody($this->buildResponseBody($e))
			->build();
	}

	/* (non-phpdoc)
	 * @see Ergo_Error_ErrorHandler::handle()
	 */
	public function handle($e)
	{
		$logger = $this->logger();
		$logger->logException($e);

		if ($this->isExceptionHalting($e))
		{
			// if headers are sent, we can't send a response
			if(headers_sent())
			{
				echo $this->buildResponseBody($e);
				exit(0);
			}

			// send it off
			$sender = new Ergo_Http_ResponseSender($this->buildResponse($e));
			$sender->send();

			if (ob_get_level() > 0) ob_flush();
			exit(self::EXIT_CODE);
		}
	}

	private function _server($var, $default)
	{
		return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
	}
}
