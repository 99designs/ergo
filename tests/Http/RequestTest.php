<?php

class Ergo_Http_RequestTest extends UnitTestCase
{
	public function testSimpleUsage()
	{
		$headers = array(
			new Ergo_Http_HeaderField('Content-Length', 9)
		);

		$request = new Ergo_Http_Request(
			Ergo_Http_Request::METHOD_GET,
			new Ergo_Http_Url('http://example.org/test/123?a=b'),
			$headers,
			'test data'
		);

		$this->assertEqual($request->getRequestMethod(), 'GET');
		$this->assertEqual($request->getUrl()->getPath(), '/test/123');
		$this->assertEqual($request->getBody(), 'test data');
		$this->assertEqual($request->getHeaders()->toArray(false), array(
			'Content-Length: 9'
			));
	}

	public function testRequestFactoryWithAbsoluteUrlInEnvironment()
	{
		$_SERVER['HTTP_HOST'] = 'example.com'; // .com
		$_SERVER['SERVER_PORT'] = '80';
		$_SERVER['REQUEST_URI'] = 'http://example.org/'; // .org
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$factory = new Ergo_Http_RequestFactory();
		$request = $factory->create();

		// I'm not sure exactly what the URL should be, but
		// there's currently a bug which is definitely less correct
		// than a URL that matches this pattern...
		$this->assertPattern(
			'#https?://example.com(:80)?/#',
			$request->getUrl()->__toString(),
			'url: %s'
		);
	}
}
