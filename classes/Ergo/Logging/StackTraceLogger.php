<?php

/**
 * A logger that appends a multiline stack trace to a file
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_StackTraceLogger extends Ergo_Logging_AbstractLogger
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
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
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
	public function logException($exception,$level=Ergo_Logger::ERROR)
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

		// add any other context
		foreach($this->_context() as $key=>$value)
		{
			$message .= "$key: $value\n";
		}

		$message .= "\n";

		$this->_appendToLogfile($message);
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

	/**
	 * Converts a PHP error int to a string
	 */
	private function _errorNumberString($intval)
	{
		$errorlevels = array(
			8191 => 'E_ALL',
			4096 => 'E_RECOVERABLE_ERROR',
			2048 => 'E_STRICT',
			1024 => 'E_USER_NOTICE',
			512 => 'E_USER_WARNING',
			256 => 'E_USER_ERROR',
			128 => 'E_COMPILE_WARNING',
			64 => 'E_COMPILE_ERROR',
			32 => 'E_CORE_WARNING',
			16 => 'E_CORE_ERROR',
			8 => 'E_NOTICE',
			4 => 'E_PARSE',
			2 => 'E_WARNING',
			1 => 'E_ERROR'
			);

		return $errorlevels[$intval];
	}
}
