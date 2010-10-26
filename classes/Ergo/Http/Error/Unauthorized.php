<?php

namespace Ergo\Http\Error;

use \Ergo\Http\Error;

class Unauthorized extends Error
{
	const STATUS_CODE=401;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
