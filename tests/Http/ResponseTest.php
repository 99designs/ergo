<?php

class Ergo_Http_ResponseTest extends UnitTestCase
{

	public function testEmptyOkResponse()
	{
		$response = new Ergo_Http_Response(200, array(), null);

		$this->assertEqual($response->getStatus()->getCode(), 200);
		$this->assertEqual($response->getHeaders()->toArray(), array());
		$this->assertFalse($response->hasBody());
	}

	public function testResponseWithBody()
	{
		$headers = array(
			new Ergo_Http_HeaderField('Content-Length', 6),
			new Ergo_Http_HeaderField('Content-Type', 'text/plain')
		);

		$response = new Ergo_Http_Response(200, $headers, 'abcdef');

		$this->assertEqual($response->getStatus()->getCode(), 200);
		$this->assertEqual($response->getHeaders()->value('Content-Length'), 6);
		$this->assertEqual($response->getHeaders()->value('Content-Type'), 'text/plain');
		$this->assertTrue($response->hasBody());
		$this->assertEqual($response->getBody(), 'abcdef');
	}

	public function testGettingAResponseHeader()
	{
		$headers = array(
			new Ergo_Http_HeaderField('Content-Length', 6),
			new Ergo_Http_HeaderField('Content-Type', 'text/plain')
		);

		$response = new Ergo_Http_Response(200, $headers, 'abcdef');
		$this->assertEqual($response->getHeaders()->value('Content-Length'),6);
	}


}
