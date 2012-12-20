<?php

namespace Ergo\Tests\Routing;

use Ergo\Routing;
use Ergo\Routing\Router;
use Ergo\Http;

class RouterTest extends \PHPUnit_Framework_TestCase
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

		$resolver = \Mockery::mock();
		$resolver
			->shouldReceive('resolve')
			->andReturn($controller, array('User.view'))
			->once()
			;

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
		$this->assertEquals($response->getHeaders()->value('Location'), '/user/24');
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
		$this->assertEquals($route->getMetadata(), array('https'=>true));
	}

	public function testCustomRoute()
	{
		$urlPath = '/user/24';

		$mockRoute = \Mockery::mock('Erg\Routing\Route');
		$mockRoute
			->shouldReceive('getMatch')
			->with($urlPath, array())
			->once()
			->andReturn(new \Ergo\Routing\RouteMatch('user', array()))
			->shouldReceive('getName')
			->andReturn('user')
			;

		$router = new Router();
		$router->connect($mockRoute, 'Custom.view');

		$this->assertEquals($router->lookup($urlPath)->getName(), 'user');
	}

	private function assertResponse($response, $body, $status=200)
	{
		$this->assertInstanceOf('\Ergo\Http\Response', $response);
		$this->assertEquals($response->getStatus()->getCode(), $status);
		$this->assertEquals($response->getBody(), $body);
	}
}
