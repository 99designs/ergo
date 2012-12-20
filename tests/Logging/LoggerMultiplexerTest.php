<?php

namespace Ergo\Tests\Logging;

use \Ergo\Logging;

class LoggerMultiplexerTest extends \PHPUnit_Framework_TestCase
{
	public function testLoggerBuiltViaConstructor()
	{
		$logger1 = \Mockery::mock();
		$logger1->shouldReceive('log')->once();
		$logger2 = \Mockery::mock();
		$logger2->shouldReceive('log')->once();

		$multiplexer = new Logging\LoggerMultiplexer(array($logger1, $logger2));
		$multiplexer->log('test');
	}

	public function testLoggerBuiltIncrementally()
	{
		$logger1 = \Mockery::mock();
		$logger1->shouldReceive('log')->once();
		$logger2 = \Mockery::mock();
		$logger2->shouldReceive('log')->once();

		$multiplexer = new Logging\LoggerMultiplexer();
		$multiplexer->addLoggers($logger1);
		$multiplexer->addLoggers(array($logger2));
		$multiplexer->addLoggers(array(null));
		$multiplexer->log('test');
	}
}
