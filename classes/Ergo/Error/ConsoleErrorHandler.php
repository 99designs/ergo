<?php

namespace Ergo\Error;

/**
 * A handler that displays an errors in the console
 */
class ConsoleErrorHandler extends AbstractErrorHandler
{
	const EXIT_CODE = 2;

	private $_showStackTrace;

	/**
	 * Constructor
	 * @param bool whether to show stack traces
	 * @param object an optional \Ergo\Logger instance
	 */
	public function __construct($showStackTrace=true, $logger=null)
	{
		parent::__construct($logger);
		$this->_showStackTrace = $showStackTrace;
	}

	/* (non-phpdoc)
	 * @see ErrorHandler::context()
	 */
	public function context()
	{
		$hostname = isset($_SERVER['HOSTNAME']) ? $_SERVER['HOSTNAME'] : 'unknown';
		$user = isset($_SERVER['USER']) ? $_SERVER['USER'] : 'unknown';

		return array(
			'Environment'=>'Console',
			'Host'=>$hostname,
			'User'=>$user,
			'Script'=>$_SERVER['SCRIPT_FILENAME'],
			'Working Dir'=>getcwd(),
			'Umask'=>sprintf("%04o", umask()),
		);
	}

	/* (non-phpdoc)
	 * @see ErrorHandler::handle()
	 */
	public function handle($e)
	{
		$logger = $this->logger();
		$logger->logException($e);

		if ($this->isExceptionHalting($e))
		{
			if (ob_get_contents() !== false) ob_end_flush();
			if($this->_showStackTrace) echo "\n".$e->__toString()."\n\n";
			exit(self::EXIT_CODE);
		}
	}
}
