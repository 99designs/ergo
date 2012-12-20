<?php

namespace Ergo\Http;

class ClientTest extends \PHPUnit_Framework_TestCase
{
	public function transport()
	{
		$transport = \Mockery::mock();
		return Client::transport($transport);
	}

	public function client()
	{
		return new Client('http://example.org');
	}

	public function testRequestMethodsDelegateToTransport()
	{
		$methods = array('GET', 'POST', 'PUT', 'DELETE');
		foreach ($methods as $method)
		{
			$transport = $this->transport();
			$transport
				->shouldReceive('send')
				->with(\Mockery::type('\Ergo\Http\Request'))
				->andReturn(new Response(200, array()))
				->once()
				;

			$client = new Client('http://example.org');
			$this->client()->$method('/hello', 'content');
		}
	}

	public function testInternalServerErrorStatusCodeThrowsException()
	{
		$this->transport()->shouldReceive('send')->andReturn(new Response(500, array()));

		$this->setExpectedException('Ergo\Http\Error');

		$this->client()->get('/hello');
	}

	public function testSetTimeoutDelegatesToTransport()
	{
		$this->transport()->shouldReceive('setTimeout')->with(4)->once();

		$this->client()->setTimeout(4);
	}

	public function testSetHttpProxyDelegatesToTransport()
	{
		$this->transport()->shouldReceive('setHttpProxy')->with('http://my.proxy.url')->once();

		$this->client()->setHttpProxy('http://my.proxy.url');
	}

	public function testSetHttpAuthDelegatesToTransport()
	{
		$this->transport()->shouldReceive('setHttpAuth')->with('username', 'password')->once();

		$this->client()->setHttpAuth('username', 'password');
	}
}
