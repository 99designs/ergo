<?php

namespace Ergo\Error;

use \Ergo;
use \Ergo\Http;

/**
 * A handler that displays an error in a web-friendly way
 */
class WebErrorHandler extends AbstractErrorHandler
{
	const EXIT_CODE = 2;

	/**
	 * Builds a response object
	 */
	protected function buildResponseBody($e)
	{
		$context = '';
		foreach(Ergo::errorContext()->export() as $key=>$value)
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
		$responseBuilder = new Http\ResponseBuilder();
		return $responseBuilder
			->setStatusCode(500)
			->notCachable()
			->setBody($this->buildResponseBody($e))
			->build();
	}

	/* (non-phpdoc)
	 * @see ErrorHandler::handle()
	 */
	public function handle($e)
	{
		$this->logger()->error($e->getMessage(), array('exception' => $e));

		if ($this->isExceptionHalting($e))
		{
			// if headers are sent, we can't send a response
			if(headers_sent())
			{
				echo $this->buildResponseBody($e);
				exit(0);
			}

			// send it off
			$sender = new Http\ResponseSender($this->buildResponse($e));
			$sender->send();

			if (ob_get_level() > 0) ob_flush();
			exit(self::EXIT_CODE);
		}
	}
}
