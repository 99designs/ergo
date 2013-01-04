<?php

namespace Ergo\Http;

class TransportTest extends \PHPUnit_Framework_TestCase
{

	protected function transportMethod($name)
	{
		$method = new \ReflectionMethod(
			'Ergo\Http\Transport', $name
		);

		$method->setAccessible(TRUE);
		return $method;
	}

	public function transport()
	{
		$transport = new Transport();
		return new Transport();
	}

	public function testIPV4Only()
	{
		$request = new Request('GET','/user/blargh');
		$family = Transport::IPFAMILY_IPV4;

		$transport = $this->transport();
		$transport->setIPFamily($family);

		$method = $this->transportMethod("_curlConnection");
		$curl = $method->invoke($transport, $request);
	}

	public function testIPV6Only()
	{
		$request = new Request('GET','/user/blargh');
		$family = Transport::IPFAMILY_IPV6;

		$transport = $this->transport();
		$transport->setIPFamily($family, $request);

		$method = $this->transportMethod("_curlConnection");
		$curl = $method->invoke($transport, $request);
	}

	public function testSetInvalidIPFamily()
	{
		$this->setExpectedException('Exception');
		$this->transport()->setIpFamily('Food goes in here');
	}
}
