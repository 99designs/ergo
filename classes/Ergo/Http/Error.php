<?php

class Ergo_Http_Error extends Ergo_Exception
{
	function getStatusCode()
	{
		return $this->getCode();
	}
}
