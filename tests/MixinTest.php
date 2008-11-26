<?php

class MyClass { function test() { return 'blargh'; } }

class Ergo_MixinTest extends UnitTestCase
{
	public function testMixin()
	{
		$mixin = new Ergo_Mixin();

		$this->assertFalse($mixin->isCallable('test'));
		$mixin->addDelegate(new MyClass());

		$this->assertTrue($mixin->isCallable('test'));
		$this->assertEqual($mixin->test(), 'blargh');
	}
}
