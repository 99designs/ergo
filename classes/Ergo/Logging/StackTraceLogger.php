<?php

namespace Ergo\Logging;

/**
 * A logger that appends a multiline stack trace to a file
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class StackTraceLogger extends AbstractLogger
{
	private $_filepath;
	private $_context;

	/**
	 * Construct
	 * @param $filepath string the path to the logfile
	 * @param $context object an optional object with a context() method that returns an array
	 */
	function __construct($filepath, $context=null)
	{
		$this->_filepath = $filepath;
		$this->_context = $context;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		return $this;
	}

	/**
	 * Helper method to render the internal context object
	 */
	private function _context()
	{
		return is_object($this->_context) ? $this->_context->context() : array();
	}

	/**
	 * Writes detailed exception information to a logfile
	 */
	public function logException($exception,$level=\Ergo\Logger::ERROR)
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
			$error = new Error($e->getSeverity());
			$message .= "Severity: $error\n";
		}

		// add any other context
		foreach($this->_context() as $key=>$value)
		{
			$message .= "$key: $value\n";
		}

		$message .= "\n";

		$this->_appendToLogfile($message);
		return $this;
	}

	/**
	 * Appends to the logfile
	 */
	private function _appendToLogfile($message)
	{
		// write to the log file
		$log = new SplFileObject($this->_filepath, "a+");
		$log->fwrite($message);
	}
}
