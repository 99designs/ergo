<?php

class Ergo_Http_StatusTest extends UnitTestCase
{

	public function testBasicUsage()
	{
		$status = new Ergo_Http_Status(200);
		$this->assertEqual($status->getCode(), 200);
		$this->assertEqual($status->getMessage(), 'OK');
		$this->assertEqual($status->getStatusLine(), "HTTP/1.1 200 OK\r\n");
	}

	public function testTeaPot()
	{
		$status = new Ergo_Http_Status(418);
		$this->assertEqual($status->getMessage(), "I'm a Teapot");
	}

}

?>
