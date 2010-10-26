<?php

namespace Ergo\Tests\Routing;

use Ergo\Http;
use Ergo\Routing;

\Mock::generate('\Ergo\Routing\ControllerFactory', 'MockControllerFactory');

class RoutedControllerTest extends \UnitTestCase implements Routing\Controller
{
	public function testConnectingAControllerInstance()
	{
		$controller = new Routing\RoutedController();
		$controller->connect('/test/path','TestPath',$this);

		$response = $controller->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function testUsingAControllerFactory()
	{
		$factory = new \MockControllerFactory();
		$factory->setReturnReference('createController',$this,array('TestPath'));
		$factory->expectOnce('createController',array('TestPath'));

		$controller = new Routing\RoutedController();
		$controller->setControllerFactory($factory);
		$controller->connect('/test/path','TestPath');

		$response = $controller->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function testNestedControllers()
	{
		$controller1 = new Routing\RoutedController();
		$controller1->connect('/test/path','Path', $this);

		$controller2 = new Routing\RoutedController();
		$controller2->connect('/test/{subpath}','Test', $controller1);

		$response = $controller2->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function execute($request)
	{
		$this->assertIsA($request,'\Ergo\Routing\RoutedRequest');

		$responseBuilder = new \Ergo\Http\ResponseBuilder();
		return $responseBuilder
			->setStatusCode(200)
			->setBody('Blargh')
			->build();
	}

	private function _createRequest($path = '/')
	{
		$url = 'http://example.org' . $path;

		return new Http\Request(Http\Request::METHOD_POST,new Http\Url($url),
			array('Content-Length' => 9),
			'test data'
		);
	}

}
