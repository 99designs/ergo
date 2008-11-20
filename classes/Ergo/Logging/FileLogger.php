<?php
/**
 * A logger that writes to a file
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_FileLogger extends Ergo_Logging_AbstractLogger
{
	private $_filepath;

	function __construct($filepath)
	{
		$this->_filepath = $filepath;
	}

	/* (non-phpdoc)
	 * @see Ergo_Logger::log()
	 */
	function log($message,$level=Ergo_Logger::INFO)
	{
		file_put_contents(
			$this->_filepath,
			$this->_buildLogMessage($message,$level)."\n",
			FILE_APPEND);
	}

	/**
	 * Construct the log message to sent to the master
	 */
	private function _buildLogMessage($message,$level)
	{
		return sprintf('[%s %s] :: %s', date("Y-m-d H:i:s"), $level, $message);
	}
}

