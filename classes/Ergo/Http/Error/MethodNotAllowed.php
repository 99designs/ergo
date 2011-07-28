<?php

namespace Ergo\Http\Error;

use \Ergo\Http\Error;

class MethodNotAllowed extends Error
{
	const STATUS_CODE=405;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
