<?php

/**
 * A basic handler for PHP errors and exceptions which consolidates errors
 * into error exceptions provides for simple logging and error formatting.
 */
class Ergo_Error_ErrorProxy
{
	private $_errorHandler;
	private $_inError=false;

	/**
	 * Constructor
	 * @param object Ergo_Error_ErrorHandler
	 */
	public function __construct($errorHandler)
	{
		$this->_errorHandler = $errorHandler;
	}

	/**
	 * Registers the error handler with PHP
	 */
	function register()
	{
		set_error_handler(array($this,'_handleError'));
		set_exception_handler(array($this,'_handleException'));
		register_shutdown_function(array($this,'_shutdown'));
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
	 * PHP exception handler interface
	 * @see set_exception_handler
	 */
	public function _handleException($e)
	{
		if($this->_inError)
		{
			echo $e->__toString();
			exit(0);
		}
		else
		{
			$this->_inError = true;
			$this->_errorHandler->handle($e);
			$this->_inError = false;
		}
	}

	/**
	 * PHP error handler interface
	 * @see set_error_handler
	 */
	public function _handleError($errno, $errstr, $errfile, $errline, $context=null)
	{
		// process errors based on the error reporting settings
		if (error_reporting() & $errno)
		{
			try
			{
				// bit of a hack to consolidate errors to exceptions
				$message = $this->_errorNumberString($errno).': '.$errstr;
				throw new ErrorException($message,0,$errno,$errfile,$errline);
			}
			catch(ErrorException $e)
			{
				$this->_handleException($e);
			}
		}
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

	/**
	 * PHP shutdown function to catch fatal errors
	 * @see http://www.eggplant.ws/blog/php/mayday-php-going-down/
	 * @see register_shutdown_function
	 */
	public function _shutdown()
	{
		if ($error = error_get_last())
		{
			try
			{
				// clear the output buffer if we can
				if(ob_get_level()>=1) ob_end_clean();

				// build an error exception
				if (isset($error['type']) && in_array($error['type'],
					array(E_ERROR, E_PARSE, E_COMPILE_ERROR)))
				{
					$this->_handleError(
						$error['type'],
						$error['message'],
						$error['file'],
						$error['line']
						);
				}
			}
			catch(Exception $e)
			{
			}
		}
	}
}
