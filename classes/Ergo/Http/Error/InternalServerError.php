<?php

class Ergo_Http_Error_InternalServerError extends Ergo_Http_Error
{
	const STATUS_CODE=500;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
