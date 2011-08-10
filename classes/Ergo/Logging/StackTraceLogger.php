<?php

namespace Ergo\Logging;

/**
 * A logger that appends a multiline stack trace to a file
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class StackTraceLogger extends AbstractLogger
{
	private $_filepath;

	/**
	 * Construct
	 * @param $filepath string the path to the logfile
	 */
	function __construct($filepath)
	{
		$this->_filepath = $filepath;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		return $this;
	}

	/**
	 * Writes detailed exception information to a logfile
	 */
	public function logException($exception,$level=\Ergo\Logger::ERROR)
	{
		$e = $exception;
		$type = ($e instanceof \ErrorException) ? 'error' : 'exception';
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
		foreach(Ergo::errorContext()->export() as $key=>$value)
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
