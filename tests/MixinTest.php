<?php

namespace Ergo\Tests\Mixin;

class MyClass { function test() { return 'blargh'; } }

class MixinTest extends \UnitTestCase
{
	public function testMixin()
	{
		$mixin = new \Ergo\Mixin();

		$this->assertFalse($mixin->isCallable('test'));
		$mixin->addDelegate(new MyClass());

		$this->assertTrue($mixin->isCallable('test'));
		$this->assertEqual($mixin->test(), 'blargh');
	}
}
