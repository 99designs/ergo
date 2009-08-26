<?php


/**
 * A console logger that includes process information
 *
 * @author Lachlan Donald <lachlan@99designs.com>
 */
class Ergo_Logging_ProcessLogger extends Ergo_Logging_ConsoleLogger
{
	protected function _getMessageFormat()
	{
		return "[process #".getmypid()." ".date("Y-m-d H:i:s")." %s] %s :: %s\n";
	}
}
