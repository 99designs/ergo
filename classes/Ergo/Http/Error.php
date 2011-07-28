<?php

namespace Ergo\Http;

class Error extends \Ergo\Exception
{
	function getStatusCode()
	{
		return $this->getCode();
	}
}
