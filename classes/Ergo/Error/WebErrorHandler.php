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
			'Server Name' => $_SERVER['SERVER_NAME'],
			'Host' => $hostname,
			'User IP' => $_SERVER['REMOTE_ADDR'],
			'User Agent' => $_SERVER['HTTP_USER_AGENT']
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
			// send it off
			$sender = new Ergo_Http_ResponseSender($this->buildResponse($e));
			$sender->send();
			exit(self::EXIT_CODE);
		}
	}
}
