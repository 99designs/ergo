<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testEmptyOkResponse()
	{
		$response = new Http\Response(200, array(), null);

		$this->assertEquals($response->getStatus()->getCode(), 200);
		$this->assertEquals($response->getHeaders()->toArray(), array());
		$this->assertFalse($response->hasBody());
	}

	public function testResponseWithBody()
	{
		$headers = array(
			new Http\HeaderField('Content-Length', 6),
			new Http\HeaderField('Content-Type', 'text/plain')
		);

		$response = new Http\Response(200, $headers, 'abcdef');

		$this->assertEquals($response->getStatus()->getCode(), 200);
		$this->assertEquals($response->getHeaders()->value('Content-Length'), 6);
		$this->assertEquals($response->getHeaders()->value('Content-Type'), 'text/plain');
		$this->assertTrue($response->hasBody());
		$this->assertEquals($response->getBody(), 'abcdef');
	}

	public function testGettingAResponseHeader()
	{
		$headers = array(
			new Http\HeaderField('Content-Length', 6),
			new Http\HeaderField('Content-Type', 'text/plain')
		);

		$response = new Http\Response(200, $headers, 'abcdef');
		$this->assertEquals($response->getHeaders()->value('Content-Length'),6);
	}

	public function testExport()
	{
		$response = new Http\Response(
			200, array('Content-Type: text/html'), 'Food goes in here'
		);

		$this->assertEquals(
			$response->export(),
			array(
				200,
				array('Content-Type: text/html'),
				'Food goes in here',
			)
		);
	}

}
