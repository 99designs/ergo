<?php

namespace Ergo\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function testPluginLifeCycle()
	{
		$plugin = \Mockery::mock('Ergo\Plugin');
		$plugin->shouldReceive('start')->once();
		$plugin->shouldReceive('stop')->once();

		$application = new \Ergo\Application();
		$application->plug($plugin);

		$application->start();
		$application->stop();
	}
}
