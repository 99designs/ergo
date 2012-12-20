<?php

namespace ergo\tests\registry;

use \Ergo\Registry;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testRegisteringObjects()
	{
		$object = new \stdClass();
		$registry = new Registry();
		$registry->register('my_key', $object);

		$this->assertTrue($registry->isRegistered('my_key'));
		$this->assertFalse($registry->isRegistered('some_other_key'));
		$this->assertSame($object, $registry->lookup('my_key'));
	}

	public function testSyntacticSugar()
	{
		$object = new \stdClass();
		$registry = new Registry();
		$registry->register('my_key', $object);

		$this->assertTrue(isset($registry->my_key));
		$this->assertFalse(isset($registry->some_other_key));
		$this->assertSame($object, $registry->my_key);
	}

	public function testLookupWithAClosure()
	{
		$registry = new Registry();
		$object = $registry->lookup('my_key', function() {
			return (object) array('test'=>'blargh');
		});

		$this->assertTrue($registry->isRegistered('my_key'));
		$this->assertEquals($object->test, 'blargh');
	}

	public function testRegisteringAFactory()
	{
		$object = (object) array('test'=>'blargh');
		$factory = \Mockery::mock('Ergo\Factory');
		$factory->shouldReceive('create')->andReturn($object)->once();

		$registry = new Registry();
		$registry->factory('my_key', $factory);

		$this->assertTrue($registry->isRegistered('my_key'));
		$this->assertSame($object, $registry->lookup('my_key'));
	}

	public function testRegisteringAClosureAsAFactory()
	{
		$registry = new Registry();
		$registry->factory('my_key', function() {
			return (object) array('test'=>'blargh');
		});

		$this->assertTrue($registry->isRegistered('my_key'));
		$this->assertEquals($registry->lookup('my_key')->test, 'blargh');
	}

	public function testTriggerOnMissWithClosure()
	{
		$callcount = 0;
		$registry = new Registry();
		$registry->trigger('my_key', function($registry) use(&$callcount) {
			$registry->register('my_key', (object) array('test'=>'blargh'));
			$callcount++;
		});

		$this->assertEquals($callcount, 0);
		$this->assertFalse($registry->isRegistered('my_key'));
		$this->assertEquals($registry->lookup('my_key')->test, 'blargh');
		$this->assertEquals($callcount, 1);
		$this->assertEquals($registry->lookup('my_key')->test, 'blargh');
		$this->assertEquals($callcount, 1);
	}

	public function testTriggerTrumpsClosureOnMiss()
	{
		$registry = new Registry();
		$registry->trigger('my_key', function($r) {
			$r->register('my_key', (object) array('source'=>'trigger'));
		});

		$result = $registry->lookup('my_key', function(){
			return (object) array('source'=>'closure');
		});

		$this->assertEquals($result->source, 'trigger');
	}
}
