<?php

namespace Ergo\Http;

class Error extends Ergo_Exception
{
	function getStatusCode()
	{
		return $this->getCode();
	}
}
