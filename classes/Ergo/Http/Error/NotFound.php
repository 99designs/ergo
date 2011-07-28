<?php

namespace Ergo\Http\Error;

use \Ergo\Http\Error;

class NotFound extends Error
{
	const STATUS_CODE=404;

	function __construct($string)
	{
		parent::__construct($string, self::STATUS_CODE);
	}
}
