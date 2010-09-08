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

	public function testDefaultValue()
	{
		$options = new Ergo_Console_Options(array('z.php'));
		$options->define(array('--blargh=24'));

		$this->assertFalse($options->has('--blargh'));
		$this->assertEqual($options->value('--blargh'), '24');
		$this->assertEqual($options->values('--blargh'), array('24'));
	}

	public function testBareParameters()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-v','myfilename'));
		$options->define(array('-v','--flag', ':filename'));

		$this->assertTrue($options->has('-v'));
		$this->assertTrue($options->has(':filename'));
		$this->assertEqual($options->value(':filename'), 'myfilename');
	}

	public function testShortParametersCanBeAggregated()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-vz','-q'));
		$options->define(array('-v','-x','-z','-q'));

		$this->assertTrue($options->has('-v'));
		$this->assertTrue($options->has('-z'));
		$this->assertTrue($options->has('-q'));
		$this->assertFalse($options->has('-x'));
	}

	public function testShortParametersWithValues()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-v', 'blargh','-v=meep'));
		$options->define(array('-v=null*'));

		$this->assertTrue($options->has('-v'));
		$this->assertEqual($options->values('-v'), array('blargh','meep'));
	}

	public function testShortParametersWithValuesAndAggregates()
	{
		$options = new Ergo_Console_Options(array('testscript.php','-vxz','-r=meep'));
		$options->define(array('-r=false*','-v','-x','-z'));

		$this->assertTrue($options->has('-v'));
		$this->assertTrue($options->has('-x'));
		$this->assertTrue($options->has('-z'));
		$this->assertEqual($options->values('-r'), array('meep'));

		$this->expectException();

		$options = new Ergo_Console_Options(array('x.php','-vrz'));
		$options
			->define(array('-v*=false','-x','-z','-r'))
			->parse()
			;
	}

	public function testMultipleParamsToHas()
	{
		$options = new Ergo_Console_Options(array('x.php','file'));
		$options->define(array('--blargh',':file'));

		$this->assertFalse($options->has('-v'));
		$this->assertTrue($options->has(':file'));
		$this->assertTrue($options->has('-v',':file'));
	}

	public function testRequiredParameters()
	{
		$options = new Ergo_Console_Options(array('x.php','-v'));
		$options->define(array('--blargh+','-v'));

		$this->assertFalse($options->has('--blargh'));
		$this->assertTrue($options->has('-v'));

		$this->assertEqual($options->errors(), array(
			'Flag --blargh is required'
			));
	}
}
