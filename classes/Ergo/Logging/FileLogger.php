<?php

namespace Ergo\Logging;

/**
 * A logger that writes to a file
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class FileLogger extends AbstractLogger
{
	private $_filepath;

	function __construct($filepath, $level=\Ergo\Logger::INFO)
	{
		parent::__construct($level);
		$this->_filepath = $filepath;
	}

	/* (non-phpdoc)
	 * @see \Ergo\Logger::log()
	 */
	function log($message,$level=\Ergo\Logger::INFO)
	{
		file_put_contents(
			$this->_filepath,
			$this->_buildLogMessage($message,$level)."\n",
			FILE_APPEND);

		return $this;
	}

	/**
	 * Construct the log message to sent to the master
	 */
	private function _buildLogMessage($message,$level)
	{
		return sprintf('[%s %s] :: %s', date("Y-m-d H:i:s"), $level, $message);
	}
}

