<?php

namespace Ergo\Http\Error;

use \Ergo\Http\Error;

class BadRequest extends Error
{
	const STATUS_CODE=400;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
