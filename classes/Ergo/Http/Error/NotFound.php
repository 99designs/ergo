<?php

class Ergo_Http_Error_NotFound extends Ergo_Http_Error
{
	const STATUS_CODE=404;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
