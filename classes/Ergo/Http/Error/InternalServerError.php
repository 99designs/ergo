<?php

namespace Ergo\Http\Error;

use \Ergo\Http\Error;

class InternalServerError extends Error
{
	const STATUS_CODE=500;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
