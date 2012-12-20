<?php

namespace Ergo\Tests\Routing;

use Ergo\Routing;
use Ergo\Routing\Router;
use Ergo\Http;

class ControllerDirectoryTest extends \PHPUnit_Framework_TestCase
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

		$this->assertEquals($controller->file, '/fake/path/LlamaController.php');
		$this->assertEquals($controller->controller, 'LlamaController');
	}

	public function testFailureToResolve()
	{
		$directory = new Routing\ControllerDirectory(new \ArrayIterator(array()));

		$this->setExpectedException('\Ergo\Exception');
		$directory->resolve('LlamaController');
	}
}
