<?php

class Ergo_Http_Error_MethodNotAllowed extends Ergo_Http_Error
{
	const STATUS_CODE=405;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
