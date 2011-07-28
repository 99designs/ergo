<?php

namespace Ergo\Tests\View;

class ViewTest extends \UnitTestCase
{
	public function setUp()
	{
		$this->_dirname = tempnam("/tmp", "viewtest");
		if(is_file($this->_dirname)) unlink($this->_dirname);
		if(!is_dir($this->_dirname)) mkdir($this->_dirname);
	}

	public function tearDown()
	{
		foreach(glob("{$this->_dirname}/*") as $file) unlink($file);
		rmdir($this->_dirname);
	}

	private function _createTemplate($name, $php)
	{
		file_put_contents("$this->_dirname/$name",
				'<?php '.$php.' ?'.'>');
		return "$this->_dirname/$name";
	}

	public function testTemplateViewsExtractVars()
	{
		$this->_createTemplate('test.php', 'echo $myvar;');

		$view = new \Ergo\View\Template();
		$view
			->includePaths($this->_dirname)
			->assign(array('myvar'=>'test'))
			->file('test.php')
			;

		$this->assertEqual($view->output(), 'test');
	}

	public function testExceptionInView()
	{
		$tpl = $this->_createTemplate('exception.php', 'throw new Exception("blargh");');

		$view = new \Ergo\View\Template();
		$view->file($tpl);

		$this->expectException();
		$view->output();
	}

	public function testTemplateAsArray()
	{
		$this->_createTemplate('test.php', 'echo $myvar;');

		$view = new \Ergo\View\Template();
		$view
			->includePaths($this->_dirname)
			->file('test.php')
			;

		$view['myvar'] = 'test';
		$this->assertEqual($view->output(), 'test');
	}

	public function testPartials()
	{
		$this->_createTemplate('test1.php',
			'echo $this->partial("test2.php",array("myvar2"=>"world"));');
		$this->_createTemplate('test2.php',
			'echo $myvar1; echo $myvar2;');

		$view = new \Ergo\View\Template();
		$view
			->includePaths($this->_dirname)
			->assign(array('myvar1'=>'hello '))
			->file('test1.php')
			;

		$this->assertEqual($view->output(), 'hello world');
	}

	public function testTemplateAsStream()
	{
		$tpl = $this->_createTemplate('test.php','echo $myvar;');

		$view = new \Ergo\View\Template($file);
		$view
			->assign(array('myvar'=>'hello world'))
			->file($tpl)
			;

		$this->assertEqual(stream_get_contents($view->stream()),
			'hello world');
	}

	public function testStringViews()
	{
		$view = new \Ergo\View\String('hello world');

		$this->assertEqual(stream_get_contents($view->stream()),'hello world');
		$this->assertEqual($view->output(),'hello world');
	}
}
