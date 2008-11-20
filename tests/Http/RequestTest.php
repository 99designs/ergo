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

}
