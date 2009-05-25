<?php

/**
 * An object for configuring php's native error and exception handling.
 */
class Ergo_ErrorHandler
{
	const EXIT_CODE=10;

	private $_logFile;
	private $_strategy;

	function __construct($logFile)
	{
		$this->_logFile = $logFile;
	}

	/**
	 * Sets the {@link Ergo_ExceptionStrategy} to use
	 */
	function setErrorStrategy($strategy)
	{
		$this->_strategy = $strategy;
		return $this;
	}

	/**
	 * Registers the error handler with PHP
	 */
	function register()
	{
		set_error_handler(array($this,'handleError'));
		set_exception_handler(array($this,'handleException'));
		return $this;
	}

	/**
	 * Attempts to unregister the error handler, restores the previous
	 */
	function unregister()
	{
		restore_error_handler();
		restore_exception_handler();
		return $this;
	}

	/**
	 * Builds a response object
	 */
	protected function buildExceptionResponse($e)
	{
		// build a response
		$responseBuilder = new Ergo_Http_ResponseBuilder();
		return $responseBuilder
			->setStatusCode(500)
			->setBody(sprintf('<h1>Error: %s</h1><pre>%s</pre>',
				$e->getMessage(),
				$e->__toString()))
			->build();
	}

	/**
	 * Writes detailed exception information to a logfile
	 */
	public function logException($exception)
	{
		$e = $exception;
		$type = ($e instanceof ErrorException) ? 'error' : 'exception';
		$header = sprintf('[%s][%s][%s]',date("c"),$type,$e->getMessage());

		$message = $header."\n\n";
		$message .= "Exception:\n";
		$message .= $e->__toString() . "\n\n";

		$message .= "Context:\n";

		// add the error details to the context
		if($type == 'error')
		{
			$message .= "Severity: ".
				$this->_errorNumberString($e->getSeverity())."\n";
		}

		$message .= "\n";

		try
		{
			// write to the log file
			$log = new SplFileObject($this->_logFile, "a+");
			$log->fwrite($message);
		}
		catch(Exception $e)
		{
			echo $e->__toString();
		}
	}

	/**
	 * PHP exception handler interface
	 * @see set_exception_handler
	 */
	public function handleException($e)
	{
		// first, log the exception
		$this->logException($e);

		if($this->_strategy)
		{
			$this->_strategy->handleException($e);
		}
		else
		{
			$this->_defaultExceptionStrategy($e);
		}
	}

	/**
	 * PHP error handler interface
	 * @see set_error_handler
	 */
	public function handleError($errno, $errstr, $errfile, $errline, $context)
	{
		// ignore suppressed errors
		if (error_reporting() === 0) return;

		try
		{
			// bit of a hack to get a decent stack trace
			throw new ErrorException(
				$this->_errorNumberString($errno).': '.$errstr,
				0,$errno,$errfile,$errline);
		}
		catch(ErrorException $e)
		{
			$this->handleException($e);
		}
	}

	/**
	 * A default behaviour for handling exceptions
	 */
	private function _defaultExceptionStrategy($e)
	{
		if(php_sapi_name() == 'cli')
		{
			echo "\n$e\n\n";
			exit(self::EXIT_CODE);
		}
		else
		{
			// send it off
			$sender = new Ergo_Http_ResponseSender($this->buildExceptionResponse($e));
			$sender->send();
			exit(0);
		}
	}

	/**
	 * Converts a PHP error int to a string
	 */
	private function _errorNumberString($intval)
	{
		$errorlevels = array(
			E_ERROR => 'E_ERROR',
			E_WARNING => 'E_WARNING',
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_ALL => 'E_ALL',
			E_STRICT => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		);

		return $errorlevels[$intval];
	}
}


