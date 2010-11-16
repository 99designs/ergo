<?php

/**
 * A class for mapping between php error codes and the string names
 */
class Ergo_Logging_Error
{
	private $_errorint;

	/**
	 * Constructor
	 */
	public function __construct($errorint)
	{
		$this->_errorint = $errorint;
	}

	/**
	 * Converts a PHP error int to a string
	 */
	public function __toString()
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

		return $errorlevels[$this->_errorint];
	}
}
