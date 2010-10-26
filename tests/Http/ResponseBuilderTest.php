<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class ResponseBuilderTest extends \UnitTestCase
{
	public function testBasicUsage()
	{
		$builder = new Http\ResponseBuilder();

		$builder
			->addHeader('Content-Type', 'text/plain')
			->setBody('test')
			->addHeader('X-Blarg', 'meh')
			->setStatusCode(418);

		$response = $builder->build();

		$this->assertIsA($response, '\Ergo\Http\Response');

		$this->assertEqual($response->getStatus()->getCode(), 418);
		$this->assertTrue($response->hasBody());
		$this->assertEqual($response->getBody(), 'test');

		$headers = $response->getHeaders()->toArray();
		$this->assertEqual(count($headers), 3, 'should be 3 headers: %s');
		$this->assertEqual($headers[0], "Content-Type: text/plain\r\n");
		$this->assertEqual($headers[1], "X-Blarg: meh\r\n");
		$this->assertEqual($headers[2], "Content-Length: 4\r\n");
	}

	public function testRedirectTemporary()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->found('http://example.org/test')
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 302);
		$this->assertFalse($response->hasBody());

		$headers = $response->getHeaders()->toArray();
		$this->assertEqual(count($headers), 2, 'should be 2 header: %s');
		$this->assertEqual($headers[0], "Location: http://example.org/test\r\n");
		$this->assertEqual($headers[1], "Content-Length: 0\r\n");
	}

	public function testRedirectPermanent()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->moved('http://example.org/test')
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 301);
		$this->assertFalse($response->hasBody());

		$headers = $response->getHeaders()->toArray();
		$this->assertEqual(count($headers), 2, 'should be 2 header: %s');
		$this->assertEqual($headers[0], "Location: http://example.org/test\r\n");
		$this->assertEqual($headers[1], "Content-Length: 0\r\n");
	}

	public function testCreated()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->created('http://example.org/test')
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 201);
		$this->assertFalse($response->hasBody());

		$headers = $response->getHeaders()->toArray();
		$this->assertEqual(count($headers), 2, 'should be 2 header: %s');
		$this->assertEqual($headers[0], "Location: http://example.org/test\r\n");
		$this->assertEqual($headers[1], "Content-Length: 0\r\n");
	}

	public function testForbidden()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->forbidden()
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 403);
		$this->assertFalse($response->hasBody());
	}

	public function testNotFound()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->notFound()
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 404);
		$this->assertFalse($response->hasBody());
	}

	public function testMethodNotAllowed()
	{
		$builder = new Http\ResponseBuilder();

		$response = $builder
			->methodNotAllowed()
			->build();

		$this->assertEqual($response->getStatus()->getCode(), 405);
		$this->assertFalse($response->hasBody());
	}

}
