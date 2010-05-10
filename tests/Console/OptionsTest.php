<?php

class Ergo_Console_OptionsTest extends UnitTestCase
{
	public function testBasicApi()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-v','--after','2008-01-01'));
		$options->define(array('--after=2009-01-01','-v','--flag'));

		$this->assertTrue($options->has('-v'));
		$this->assertFalse($options->has('--flag'));
		$this->assertTrue($options->has('--after'));
		$this->assertEqual($options->value('--after'), '2008-01-01');
		$this->assertEqual($options->values('--after'), array('2008-01-01'));
	}

	public function testSyntacticSugar()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-v','--after','2008-01-01'));
		$options->define(array('--after=2009-01-01','-v','--flag'));

		$this->assertTrue(isset($options->v));
		$this->assertTrue(isset($options->after));
		$this->assertFalse(isset($options->flag));
		$this->assertEqual($options->after, '2008-01-01');
	}

	public function testBareParameters()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-v','myfilename'));
		$options->define(array('-v','--flag', ':filename'));

		$this->assertTrue($options->has('-v'));
		$this->assertTrue($options->has(':filename'));
		$this->assertEqual($options->value(':filename'), 'myfilename');
	}

	public function testMultipleParamsToHas()
	{
		$options = new Ergo_Console_Options(array('x.php','file'));
		$options->define(array('--blargh',':file'));

		$this->assertFalse($options->has('-v'));
		$this->assertTrue($options->has(':file'));
		$this->assertTrue($options->has('-v',':file'));
	}
}
