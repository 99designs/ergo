<?php

Mock::generate('Ergo_Plugin','Ergo_MockPlugin');

class Ergo_ApplicationTest extends UnitTestCase
{
	public function testPluginLifeCycle()
	{
		$plugin = new Ergo_MockPlugin();
		$plugin->expectOnce('start');
		$plugin->expectOnce('stop');

		$application = new Ergo_Application();
		$application->plug($plugin);

		$application->start();
		$application->stop();
	}
}
