<?php

class Ergo_Http_Error_BadRequest extends Ergo_Http_Error
{
	const STATUS_CODE=400;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
