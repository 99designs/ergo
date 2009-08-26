<?php


/**
 * A simple logger for the console
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_ConsoleLogger extends Ergo_Logging_AbstractLogger
{
	private $_ansi;

	/**
	 * Construct a logger with a default level of trace
	 */
	function __construct($ansi=false, $level=Ergo_Logger::INFO)
	{
		parent::__construct($level);
		$this->_ansi = $ansi;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		if($this->_isLevelEnabled($level))
		{
			echo $this->_getMessagePrefix($level);

			printf(
				$this->_getMessageFormat(),
				$level,
				$this->_getMemoryUsage(),
				$message
				);

			echo $this->_getMessageSuffix($level);
		}
	}

	/**
	 * Returns the message format used by the logger, parametized with
	 * the level, memory usage as a string and the message
	 * @see sprintf
	 */
	protected function _getMessageFormat()
	{
		return "[".date("Y-m-d H:i:s")." %s] %s :: %s\n";
	}

	/**
	 * Build a message prefix
	 */
	protected function _getMessagePrefix($level)
	{
		return $this->_ansi ?
			"\033[".$this->_getAnsiColor($level)."m" :
			"";
	}

	/**
	 * Build a message suffix
	 */
	protected function _getMessageSuffix($level)
	{
		return $this->_ansi ?
			"\033[0m" :
			"";
	}

	/**
	 * Return an ANSI color for particular error levels
	 */
	private function _getAnsiColor($level)
	{
		switch($level)
		{
			case Ergo_Logger::ERROR: return 31;
			case Ergo_Logger::WARN: return 33;
			default: return 39;
		}
	}

	/**
	 * Gets the memory usage where available
	 */
	private function _getMemoryUsage()
	{
		if(function_exists('memory_get_usage'))
		{
			return
				number_format(round(memory_get_usage()/1024)) ."/".
				number_format(round(memory_get_peak_usage()/1024)) ."Kb mem/peak";
		}
	}
}
