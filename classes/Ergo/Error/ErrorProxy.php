<?php

/**
 * A basic handler for PHP errors and exceptions which consolidates errors
 * into error exceptions provides for simple logging and error formatting.
 */
class Ergo_Error_ErrorProxy
{
	private $_application;
	private $_inError=false;
	private $_registered=false;

	/**
	 * Constructor
	 * @param object Ergo_Error_ErrorHandler
	 */
	public function __construct($application)
	{
		$this->_application = $application;
	}

	/**
	 * Registers the error handler with PHP
	 */
	function register()
	{
		set_error_handler(array($this,'_handleError'));
		set_exception_handler(array($this,'_handleException'));
		register_shutdown_function(array($this,'_shutdown'));
		$this->_registered = true;
		return $this;
	}

	/**
	 * Attempts to unregister the error handler, restores the previous
	 */
	function unregister()
	{
		restore_error_handler();
		restore_exception_handler();
		$this->_registered = false;
		return $this;
	}

	/**
	 * PHP exception handler interface
	 * @see set_exception_handler
	 */
	public function _handleException($e)
	{
		if(!$this->_inError && $handler = $this->_application->errorHandler())
		{
			$this->_inError = true;
			$handler->handle($e);
			$this->_inError = false;
		}
		else
		{
			if(php_sapi_name() == 'cli')
			{
				echo $e->__toString();
				exit(1);
			}
			else
			{
				header('HTTP/1.1 500 Internal Server Error');
				echo "<h1>Error: ".$e->getMessage().'</h1>';
				echo '<pre>'.$e->__toString().'</pre>';
				exit(1);
			}
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
			E_ALL => 'E_ALL',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_STRICT => 'E_STRICT',
			E_USER_NOTICE => 'E_USER_NOTICE',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_ERROR => 'E_USER_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_NOTICE => 'E_NOTICE',
			E_PARSE => 'E_PARSE',
			E_WARNING => 'E_WARNING',
			E_ERROR => 'E_ERROR'
		);

		if (defined('E_DEPRECATED'))
		{
			$errorlevels[E_DEPRECATED] = 'E_DEPRECATED';
			$errorlevels[E_USER_DEPRECATED] = 'E_USER_DEPRECATED';
		}

		return $errorlevels[$intval];
	}

	/**
	 * PHP shutdown function to catch fatal errors
	 * @see http://www.eggplant.ws/blog/php/mayday-php-going-down/
	 * @see register_shutdown_function
	 */
	public function _shutdown()
	{
		if ($this->_registered && $error = error_get_last())
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
