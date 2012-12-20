<?php

namespace Ergo\Tests\Http;

use Ergo\Http;
use Ergo\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpleUsage()
	{
		$headers = array(
			new Http\HeaderField('Content-Length', 9)
		);

		$request = new Http\Request(
			Request::METHOD_GET,
			new Http\Url('http://example.org/test/123?a=b'),
			$headers,
			'test data'
		);

		$this->assertEquals($request->getRequestMethod(), 'GET');
		$this->assertEquals($request->getUrl()->getPath(), '/test/123');
		$this->assertEquals($request->getBody(), 'test data');
		$this->assertEquals($request->getHeaders()->toArray(false), array(
			'Content-Length: 9'
			));
	}

	public function testExport()
	{
		$request = new Request(
			Request::METHOD_GET,
			'http://example.org',
			array('Accept: text/html'),
			'test data'
		);

		$this->assertEquals(
			$request->export(),
			array(
				'GET',
				'http://example.org/',
				array('Accept: text/html'),
				'test data',
			)
		);
	}

	public function testRequestFactoryWithAbsoluteUrlInEnvironment()
	{
		$server = array(
			'SERVER_NAME' => 'example.com', // .com
			'HTTP_HOST' => 'example.com', // .com
			'SERVER_PORT' => '80',
			'REQUEST_URI' => 'http://example.org/', // .org
			'REQUEST_METHOD' => 'GET',
			);

		$factory = new Http\RequestFactory($server);
		$request = $factory->create();

		// I'm not sure exactly what the URL should be, but
		// there's currently a bug which is definitely less correct
		// than a URL that matches this pattern...
		$this->assertRegExp(
			'#https?://example.com(:80)?/#',
			$request->getUrl()->__toString(),
			'url: %s'
		);
	}

  public function testRequestFactoryWithoutRequestMethod()
  {
    $server = array();

		$factory = new Http\RequestFactory($server);
		$request = $factory->create();
    $this->assertInstanceOf('Ergo\Http\NullRequest', $request);
  }

	public function testRequestFactorySchemeHeader()
	{
		$server = array(
			'SERVER_NAME' => 'example.com', // .com
			'HTTP_HOST' => 'example.com', // .com
			'SERVER_PORT' => '80',
			'REQUEST_URI' => 'http://example.com/', // .org
			'REQUEST_METHOD' => 'GET',
			'HTTP_X_FORWARDED_PROTO' => 'https',
			);

		$factory = new Http\RequestFactory($server);
		$factory->setSchemeHeader('X-Forwarded-Proto');
		$request = $factory->create();

		$this->assertEquals($request->getRequestMethod(), 'GET');
		$this->assertEquals((string) $request->getUrl(), 'https://example.com/');
		$this->assertEquals($request->getUrl()->getScheme(), 'https');
	}
}
