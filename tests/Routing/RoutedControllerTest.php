<?php

//Mock::generate('Ergo_Routing_Controller', 'MockController');
Mock::generate('Ergo_Routing_ControllerFactory', 'MockControllerFactory');

class Ergo_Routing_RoutedControllerTest
	extends UnitTestCase
	implements Ergo_Routing_Controller
{
	public function testConnectingAControllerInstance()
	{
		$controller = new Ergo_Routing_RoutedController();
		$controller->connect('/test/path','TestPath',$this);

		$response = $controller->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function testUsingAControllerFactory()
	{
		$factory = new MockControllerFactory();
		$factory->setReturnReference('createController',$this,array('TestPath'));
		$factory->expectOnce('createController',array('TestPath'));

		$controller = new Ergo_Routing_RoutedController();
		$controller->setControllerFactory($factory);
		$controller->connect('/test/path','TestPath');

		$response = $controller->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function testNestedControllers()
	{
		$controller1 = new Ergo_Routing_RoutedController();
		$controller1->connect('/test/path','Path', $this);

		$controller2 = new Ergo_Routing_RoutedController();
		$controller2->connect('/test/{subpath}','Test', $controller1);

		$response = $controller2->execute($this->_createRequest('/test/path'));
		$this->assertEqual($response->getBody(), 'Blargh');
	}

	public function execute($request)
	{
		$this->assertIsA($request,'Ergo_Routing_RoutedRequest');

		$responseBuilder = new Ergo_Http_ResponseBuilder();
		return $responseBuilder
			->setStatusCode(200)
			->setBody('Blargh')
			->build();
	}

	private function _createRequest($path = '/')
	{
		$url = 'http://example.org' . $path;

		return new Ergo_Http_Request(
			Ergo_Http_Request::METHOD_POST,
			new Ergo_Http_Url($url),
			array('Content-Length' => 9),
			'test data'
		);
	}

}
