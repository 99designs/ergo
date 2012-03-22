<?php

namespace Ergo\Tests\Routing;

use Ergo\Routing;
use Ergo\Routing\Router;
use Ergo\Http;

\Mock::generate('\Ergo\Routing\ControllerResolver', 'MockControllerResolver');
\Mock::generate('\Ergo\Routing\Route', 'MockRoute');

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
		$this->assertResponse($response, 'connected');
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
		$this->assertResponse($response, 'llamas rock');
	}

	public function testConnectingARedirectRoute()
	{
		$router = new Router();
		$router->connect('/user/{userid}', 'User.view');
		$router->redirect('/user/alias/{userid}', 'Alias.view', 'User.view');

		$response = $router->execute(new Http\Request('GET','/user/alias/24'));
		$this->assertResponse($response, NULL, 302);
		$this->assertEqual($response->getHeaders()->value('Location'), '/user/24');
	}

	public function testConnectingAnAliasRoute()
	{
		$controller = new Routing\CallbackController(function($request, $builder){
			return $builder
				->setBody($request->getRouteMatch()->getName())
				->build();
		});

		$router = new Router();
		$router->connect('/user/{userid}', 'User.view', $controller);
		$router->alias('/user/alias/{userid}', 'Alias.view', 'User.view');

		$response = $router->execute(new Http\Request('GET','/user/alias/24'));
		$this->assertResponse($response, 'Alias.view');
	}

	public function testRouteMetadata()
	{
		$router = new Router();
		$router
			->connect('/user/{userid}', 'User.view', 'User', array('https'=>true))
			;

		$route = $router->lookup('/user/24');
		$this->assertEqual($route->getMetadata(), array('https'=>true));
	}

	public function testCustomRoute()
	{
		$urlPath = '/user/24';

		$mockRoute = new \MockRoute();
		$mockRoute->setReturnValue('getMatch',true);
		$mockRoute->expectOnce('getMatch',array($urlPath,array()));

		$router = new Router();
		$router->connect($mockRoute, 'Custom.view');

		$route = $router->lookup($urlPath);
	}

	private function assertResponse($response, $body, $status=200)
	{
		$this->assertIsA($response, '\Ergo\Http\Response');
		$this->assertEqual($response->getStatus()->getCode(), $status);
		$this->assertEqual($response->getBody(), $body);
	}
}
