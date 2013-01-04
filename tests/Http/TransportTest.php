<?php

namespace Ergo\Http;

class TransportTest extends \PHPUnit_Framework_TestCase
{
	public function transport()
	{
		return new Transport();
	}

	public function testIPV4Only()
	{
		$this->transport()->setIPFamily(Transport::IPFAMILY_IPV4);
	}

	public function testIPV6Only()
	{
		$this->transport()->setIPFamily(Transport::IPFAMILY_IPV6);
	}
	public function testSetInvalidIPFamily()
	{
		$this->setExpectedException('Exception');
		$this->transport()->setIpFamily('Food goes in here');
	}
}
