<?php

namespace Ergo\Tests\Logging;

use \Ergo\Logging;

\Mock::generate('\Ergo\Logger','MockLogger');

class LoggerMultiplexerTest extends \UnitTestCase
{
	public function testLoggerBuiltViaConstructor()
	{
		$logger1 = new \MockLogger();
		$logger1->expectOnce('log');
		$logger2 = new \MockLogger();
		$logger2->expectOnce('log');

		$multiplexer = new Logging\LoggerMultiplexer(array($logger1, $logger2));
		$multiplexer->log('test');
	}

	public function testLoggerBuiltIncrementally()
	{
		$logger1 = new \MockLogger();
		$logger1->expectOnce('log');
		$logger2 = new \MockLogger();
		$logger2->expectOnce('log');

		$multiplexer = new Logging\LoggerMultiplexer();
		$multiplexer->addLoggers($logger1);
		$multiplexer->addLoggers(array($logger2));
		$multiplexer->addLoggers(array(null));
		$multiplexer->log('test');
	}
}
