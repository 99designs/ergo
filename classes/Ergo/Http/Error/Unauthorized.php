<?php

class Ergo_Http_Error_Unauthorized extends Ergo_Http_Error
{
	const STATUS_CODE=401;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
