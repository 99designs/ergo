<?php

namespace Ergo\Tests\Routing;

use Ergo\Routing;
use Ergo\Routing\Router;
use Ergo\Http;

\Mock::generate('\Ergo\Routing\ControllerResolver', 'MockControllerResolver');

class ControllerDirectoryTest extends \UnitTestCase
{
	public function testControllerDirectory()
	{
		$files = new \ArrayIterator(array(
			new \SplFileInfo('/fake/path/TestController.php'),
			new \SplFileInfo('/fake/path/LlamaController.php'),
			new \SplFileInfo('/fake/path/LongNameController.php')
			));

		$directory = new Routing\ControllerDirectory($files, function($f, $c) use(&$result) {
			return (object) array(
				'file'=>$f,
				'controller'=>$c,
				);
		});

		$controller = $directory->resolve('LlamaController');

		$this->assertEqual($controller->file, '/fake/path/LlamaController.php');
		$this->assertEqual($controller->controller, 'LlamaController');
	}

	public function testFailureToResolve()
	{
		$directory = new Routing\ControllerDirectory(new \ArrayIterator(array()));

		$this->expectException('\Ergo\Exception');
		$directory->resolve('LlamaController');
	}
}
