<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class StatusTest extends \PHPUnit_Framework_TestCase
{
	public function testBasicUsage()
	{
		$status = new Http\Status(200);
		$this->assertEquals($status->getCode(), 200);
		$this->assertEquals($status->getMessage(), 'OK');
		$this->assertEquals($status->getStatusLine(), "HTTP/1.1 200 OK\r\n");
	}

	public function testTeaPot()
	{
		$status = new Http\Status(418);
		$this->assertEquals($status->getMessage(), "I'm a Teapot");
	}
}
