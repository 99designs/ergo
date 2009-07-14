<?php

Mock::generate('Ergo_Logger','Ergo_MockLogger');

class Ergo_Logging_LoggerMultiplexerTest extends UnitTestCase
{
	public function testLoggerBuiltViaConstructor()
	{
		$logger1 = new Ergo_MockLogger();
		$logger1->expectOnce('log');
		$logger2 = new Ergo_MockLogger();
		$logger2->expectOnce('log');

		$multiplexer = new Ergo_Logging_LoggerMultiplexer(array($logger1, $logger2));
		$multiplexer->log('test');
	}

	public function testLoggerBuiltIncrementally()
	{
		$logger1 = new Ergo_MockLogger();
		$logger1->expectOnce('log');
		$logger2 = new Ergo_MockLogger();
		$logger2->expectOnce('log');

		$multiplexer = new Ergo_Logging_LoggerMultiplexer();
		$multiplexer->addLoggers($logger1);
		$multiplexer->addLoggers(array($logger2));
		$multiplexer->addLoggers(array(null));
		$multiplexer->log('test');
	}
}
