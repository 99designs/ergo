<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class StatusTest extends \UnitTestCase
{
	public function testBasicUsage()
	{
		$status = new Http\Status(200);
		$this->assertEqual($status->getCode(), 200);
		$this->assertEqual($status->getMessage(), 'OK');
		$this->assertEqual($status->getStatusLine(), "HTTP/1.1 200 OK\r\n");
	}

	public function testTeaPot()
	{
		$status = new Http\Status(418);
		$this->assertEqual($status->getMessage(), "I'm a Teapot");
	}
}
