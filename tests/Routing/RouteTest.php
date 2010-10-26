<?php

namespace Ergo\Tests\Routing;

use Ergo\Http;
use Ergo\Routing;

\Mock::generate('\Ergo\Routing\Controller','Routing\MockController');

class RouteTest extends \UnitTestCase
{
	private $_exampleRoutes = array(
		'/fruits' => 'fruits',
		'/fruits/{fruitid}' => 'fruit',
		'/fruits/{fruitid}/flavours/{flavourid}' => 'flavour',
	);

	public function testRouteLookup()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);

		$this->_assertRoute($router,'/fruits','fruits',
			array()
		);

		$this->_assertRoute($router,'/fruits/123','fruit',
			array('fruitid' => 123)
		);

		$this->_assertRoute($router,'/fruits/123/flavours/456','flavour',
			array('fruitid' => 123, 'flavourid' => 456)
		);
	}

	public function testRouterTrimsTrailingSlashes()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);

		$this->_assertRoute($router,'/fruits/','fruits',
			array()
		);
	}

	public function testRouteBuild()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);

		$this->assertEqual(
			$router->buildUrl('fruits'),
			'/fruits'
		);

		$this->assertEqual(
			$router->buildUrl('fruit', array('fruitid' => 234)),
			'/fruits/234'
		);

		$this->assertEqual(
			$router->buildUrl('flavour', array('fruitid' => 234, 'flavourid' => 456)),
			'/fruits/234/flavours/456'
		);
	}

	public function testRouteLookupFailsOnNonExistentRouteName()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);
		$this->expectException('\Ergo\Routing\LookupException');
		$router->lookup('/blarg');
	}

	public function testRouteLookupFailsWithEmptyTemplateVars()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);
		$this->expectException('\Ergo\Routing\LookupException');
		$router->lookup('/fruits//flavours/');
	}

	public function testRouteBuildFailsWithExtraParam()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);
		$this->expectException('\Ergo\Routing\BuildException');
		$router->buildUrl('fruits', array('test' => 123));
	}

	public function testRouteBuildFailsWithMissingParam()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->map($template, $name);
		$this->expectException('\Ergo\Routing\BuildException');
		$router->buildUrl('flavours');
	}

	public function testRouterMatchArrayInterface()
	{
		$match = new Routing\RouterMatch('test', array('a' => 'b'));
		$this->assertEqual($match['a'], 'b');
	}


	public function testRoutesWithStringTypes()
	{
		$router = new Routing\Router();
		$router->map('/fruits/{fruitname:string}','fruit');

		$this->_assertRoute($router,'/fruits/blargh','fruit',
			array('fruitname'=>'blargh')
		);

		$this->_assertNoRoute($router,'/fruits/blargh;.');
	}

	public function testRoutesWithIntegerTypes()
	{
		$router = new Routing\Router();
		$router->map('/fruits/{fruitid:int}','fruit');

		$this->_assertRoute($router,'/fruits/123','fruit',
			array('fruitid'=>'123')
		);

		$this->_assertNoRoute($router,'/fruits/123;blargh');
		$this->_assertNoRoute($router,'/fruits/blargh');
	}

	public function testRoutesWithEnumTypes()
	{
		$router = new Routing\Router();
		$router->map('/fruits/{fruittype:(orange|apple)}','fruit');

		$this->_assertRoute($router,'/fruits/apple','fruit',
			array('fruittype'=>'apple')
		);

		$this->_assertNoRoute($router,'/fruits/pear');
		$this->_assertNoRoute($router,'/fruits/llama');
	}

	public function testSimpleStarRoutes()
	{
		$router = new Routing\Router();
		$router->map('/fruits/*','fruit');

		$this->_assertRoute($router,'/fruits/this/is/a/test','fruit');
		$this->_assertNoRoute($router,'/blargh');
	}

	public function testStarRoutesWithParameters()
	{
		$router = new Routing\Router();
		$router->map('/fruits/{fruitid}/*','fruit');

		$this->_assertRoute($router,'/fruits/5/this/is/a/test','fruit',array(
			'fruitid'=>5
			));
		$this->_assertNoRoute($router,'/blargh');
	}

	public function testStarRoutesMatchNothing()
	{
		$router = new Routing\Router();
		$router->map('/*','default');

		$this->_assertRoute($router,'/','default');
		$this->_assertRoute($router,'/this/is/a/test','default');
	}

	public function testInterpolationFailsWithStarRoutes()
	{
		$router = new Routing\Router();
		$router->map('/{fruit}/*','fruit');

		$this->expectException();
		$router->buildUrl('fruit', array('fruit' =>'apple'));
	}

	// ----------------------------------------

	private function _assertRoute($map, $template, $name, $parameters=false)
	{
		$match = $map->lookup($template);
		$this->assertEqual($match->getName(), $name);
		if($parameters) $this->assertEqual($match->getParameters(), $parameters);
	}

	private function _assertNoRoute($map, $template)
	{
		try
		{
			$map->lookup($template);
			$this->fail("should fail route lookup for $template");
		}
		catch(Routing\LookupException $e)
		{
			$this->assertTrue(true);
		}
	}

}
