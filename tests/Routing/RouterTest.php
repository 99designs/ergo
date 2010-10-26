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
		$router->connect('/user/alias/{userid}', 'Alias.view', 'redirect:User.view');

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
		$router->connect('/user/alias/{userid}', 'Alias.view', 'alias:User.view');

		$response = $router->execute(new Http\Request('GET','/user/alias/24'));
		$this->assertResponse($response, 'Alias.view');
	}

	public function testCustomControllerPrefix()
	{
		$router = new Router();
		$router
			->connect('/user/{userid}', 'Alias.view', 'llama:test')
			->prefix('llama', function($string) {
				return new Routing\CallbackController(function($request, $builder) use($string) {
					return $builder
						->setBody($string)
						->build();
					});
				});

		$response = $router->execute(new Http\Request('GET','/user/24'));
		$this->assertResponse($response, 'test');
	}

	private function assertResponse($response, $body, $status=200)
	{
		$this->assertIsA($response, '\Ergo\Http\Response');
		$this->assertEqual($response->getStatus()->getCode(), $status);
		$this->assertEqual($response->getBody(), $body);
	}
}
