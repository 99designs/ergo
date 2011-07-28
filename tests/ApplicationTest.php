<?php

namespace ergo\tests\application;

\Mock::generate('\Ergo\Plugin','MockPlugin');

class ApplicationTest extends \UnitTestCase
{
	public function testPluginLifeCycle()
	{
		$plugin = new \MockPlugin();
		$plugin->expectOnce('start');
		$plugin->expectOnce('stop');

		$application = new \Ergo\Application();
		$application->plug($plugin);

		$application->start();
		$application->stop();
	}
}
