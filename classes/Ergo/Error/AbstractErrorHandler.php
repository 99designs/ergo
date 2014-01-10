<?php

namespace Ergo\Error;

use Ergo\Logging;

/**
 * A basic handler for PHP errors and exceptions which consolidates errors
 * into error exceptions provides for simple logging and error formatting.
 */
abstract class AbstractErrorHandler implements ErrorHandler
{
	private $_proxy;
	private $_logger;

	/**
	 * Constructor
	 * @param object an optional \Ergo\Logger instance
	 */
	public function __construct($logger=null)
	{
		if(is_object($logger))
		{
			$this->_logger = new Logging\LoggerMultiplexer();
			$this->_logger->addLoggers($logger);
		}
	}

	/* (non-phpdoc)
	 * @see \Ergo\Error\ErrorHandler::logger()
	 */
	public function logger()
	{
		if(!isset($this->_logger))
		{
			$this->_logger = new Logging\LoggerMultiplexer();
		}

		return $this->_logger;
	}

	/**
	* Determines whether an exception is recoverable
	* @return bool
	*/
	protected function isExceptionRecoverable($e)
	{
		if ($e instanceof \ErrorException)
		{
			$ignore = E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT;
			return (($ignore & $e->getSeverity()) != 0);
		}

		return false;
	}

	/**
	* Determines whether the exception should halt execution
	* @return bool
	*/
	protected function isExceptionHalting($e)
	{
		return true;
	}

	public function logPhpError($errno, $errstr, $errfile, $errline)
	{
		$error = new Logging\Error($errno);
		$this->logger()->error("PHP Error $error: $errstr in $errfile:$errline");
	}

	public function logException($exception)
	{
		$this->logger()->error(
			sprintf(
				"Exception '%s' with message '%s' in %s:%d",
				get_class($exception),
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine()
			),
			array('exception' => $exception)
		);
	}
}
