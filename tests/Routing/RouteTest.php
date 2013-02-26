<?php

namespace Ergo\Tests\Routing;

use Ergo\Http;
use Ergo\Routing;

class RouteTest extends \PHPUnit_Framework_TestCase
{
	private $_exampleRoutes = array(
		'/fruits' => 'fruits',
		'/fruits/{fruitid}' => 'fruit',
		'/fruits/{fruitid}/flavours/{flavourid}' => 'flavour',
	);

	public function testRouteLookup()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);

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
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);

		$this->_assertRoute($router,'/fruits/','fruits',
			array()
		);
	}

	public function testRouteBuild()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);

		$this->assertEquals(
			$router->buildUrl('fruits'),
			'/fruits'
		);

		$this->assertEquals(
			$router->buildUrl('fruit', array('fruitid' => 234)),
			'/fruits/234'
		);

		$this->assertEquals(
			$router->buildUrl('flavour', array('fruitid' => 234, 'flavourid' => 456)),
			'/fruits/234/flavours/456'
		);
	}

	public function testRouteLookupFailsOnNonExistentRouteName()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);
		$this->setExpectedException('\Ergo\Routing\LookupException');
		$router->lookup('/blarg');
	}

	public function testRouteLookupFailsWithEmptyTemplateVars()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);
		$this->setExpectedException('\Ergo\Routing\LookupException');
		$router->lookup('/fruits//flavours/');
	}

	public function testRouteBuildFailsWithExtraParam()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);
		$this->setExpectedException('\Ergo\Routing\BuildException');
		$router->buildUrl('fruits', array('test' => 123));
	}

	public function testRouteBuildFailsWithMissingParam()
	{
		$router = new Routing\Router();
		foreach($this->_exampleRoutes as $template=>$name) $router->connect($template, $name);
		$this->setExpectedException('\Ergo\Routing\LookupException');
		$router->buildUrl('flavours');
	}

	public function testRouteMatchGetterInterface()
	{
		$match = new Routing\RouteMatch('test', array('a' => 'b'));
		$this->assertEquals($match->a, 'b');
	}

	public function testRoutesWithStringTypes()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/{fruitname:string}','fruit');

		$this->_assertRoute($router,'/fruits/blargh','fruit',
			array('fruitname'=>'blargh')
		);

		$this->_assertNoRoute($router,'/fruits/blargh;.');
	}

	public function testRoutesWithIntegerTypes()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/{fruitid:int}','fruit');

		$this->_assertRoute($router,'/fruits/123','fruit',
			array('fruitid'=>'123')
		);

		$this->_assertNoRoute($router,'/fruits/123;blargh');
		$this->_assertNoRoute($router,'/fruits/blargh');
	}

	public function testRoutesWithEnumTypes()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/{fruittype:(orange|apple)}','fruit');

		$this->_assertRoute($router,'/fruits/apple','fruit',
			array('fruittype'=>'apple')
		);

		$this->_assertNoRoute($router,'/fruits/pear');
		$this->_assertNoRoute($router,'/fruits/llama');
	}

	public function testSimpleStarRoutes()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/*','fruit');

		$this->_assertRoute($router,'/fruits/this/is/a/test','fruit');
		$this->_assertNoRoute($router,'/blargh');
	}

	public function testStarRoutesWithParameters()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/{fruitid}/*','fruit');

		$this->_assertRoute($router,'/fruits/5/this/is/a/test','fruit',array(
			'fruitid'=>5
			));
		$this->_assertNoRoute($router,'/blargh');
	}

	public function testStarRoutesMatchNothing()
	{
		$router = new Routing\Router();
		$router->connect('/*','default');

		$this->_assertRoute($router,'/','default');
		$this->_assertRoute($router,'/this/is/a/test','default');
	}

	public function testInterpolationFailsWithStarRoutes()
	{
		$router = new Routing\Router();
		$router->connect('/{fruit}/*','fruit');

		$this->setExpectedException('Ergo\Routing\Exception');
		$router->buildUrl('fruit', array('fruit' =>'apple'));
	}

	public function testGreedyParametersInRoutes()
	{
		$router = new Routing\Router();
		$router->connect('/fruits/{fruitid}/{blah:greedy}','fruit');

		$this->_assertRoute($router,'/fruits/5/this/is/a/test','fruit',array(
			'fruitid'=>5,
			'blah'=>'this/is/a/test',
			));
		$this->_assertNoRoute($router,'/blargh');
	}

	// ----------------------------------------

	private function _assertRoute($connect, $template, $name, $parameters=false)
	{
		$match = $connect->lookup($template);
		$this->assertEquals($match->getName(), $name);
		if($parameters) $this->assertEquals($match->getParameters(), $parameters);
	}

	private function _assertNoRoute($connect, $template)
	{
		try
		{
			$connect->lookup($template);
			$this->fail("should fail route lookup for $template");
		}
		catch(Routing\LookupException $e)
		{
			$this->assertTrue(true);
		}
	}

}
