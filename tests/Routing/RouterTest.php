<?php

namespace Ergo\Tests\Routing;

use Ergo\Routing;
use Ergo\Routing\Router;
use Ergo\Http;

\Mock::generate('\Ergo\Routing\ControllerResolver', 'MockControllerResolver');

class RouterTest extends \UnitTestCase
{
	public function testConnectingAClosureAsARoute()
	{
		$router = new Router();
		$router->connect('/user/{userid}', 'User.view', function($request, $builder) {
			return $builder
				->setBody('connected')
				->build();
		});

		$response = $router->execute(new Http\Request('GET','/user/24'));
		$this->assertIsA($response, '\Ergo\Http\Response');
		$this->assertEqual($response->getBody(), 'connected');
	}

	public function testUsingAControllerResolver()
	{
		$controller = new Routing\CallbackController(function($request, $builder){
			return $builder
				->setBody('llamas rock')
				->build();
		});

		$resolver = new \MockControllerResolver();
		$resolver->expectOnce('resolve');
		$resolver->setReturnReference('resolve', $controller, array('User.view'));

		$router = new Router($resolver);
		$router->connect('/user/{userid}', 'User.view');

		$response = $router->execute(new Http\Request('GET','/user/24'));
		$this->assertIsA($response, '\Ergo\Http\Response');
		$this->assertEqual($response->getBody(), 'llamas rock');
	}

	public function testConnectingAStringAsARoute()
	{
		$router = new Router();
		$router->connect('/user/{userid}', 'User.view');
		$router->connect('/user/alias/{userid}', 'Alias.view', 'redirect:User.view');

		$response = $router->execute(new Http\Request('GET','/user/alias/24'));
		$this->assertIsA($response, '\Ergo\Http\Response');
		$this->assertEqual($response->getStatus()->getCode(), 302);
		$this->assertEqual($response->getHeaders()->value('Location'), '/user/24');
	}
}
