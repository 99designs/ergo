<?php

namespace Ergo\Config;

class FileConfigTest extends \PHPUnit_Framework_TestCase
{

	private $_files = array();

	public function testFileConfigLoadsConfigFromFiles()
	{
		$file = $this->_createConfig(array('var' => 'value'));
		$config = new FileConfig(array($file));
		$this->assertEquals($config->get('var'), 'value');
	}

	public function testFileConfigOverlaysConfigFilesInOrderTheyAreLoaded()
	{
		$one = $this->_createConfig(array('red' => 'green', 'blue' => 'yellow'));
		$two = $this->_createConfig(array('red' => 'red'));

		$config = new FileConfig();
		$config->loadFile($one);
		$config->loadFile($two);

		$this->assertEquals($config->get('red'), 'red');
		$this->assertEquals($config->get('blue'), 'yellow');
	}

	public function testFileConfigSilentlyIgnoresMissingOptionalConfigFiles()
	{
		$config = new FileConfig();
		$config->loadFile('/some/non/existent/dir/config.php', true, 'config');
	}

	public function setUp()
	{
		$this->_files = array();
	}

	public function teardown()
	{
		foreach ($this->_files as $file)
		{
			if (file_exists($file))
				unlink($file);
		}
	}

	private function _createConfig($data)
	{
		$config = "<?php return array(\n";
		foreach ($data as $key => $value)
		{
			$config .= "'$key' => '$value',\n";
		}
		$config .= ");\n";
		$file = tempnam('/tmp', 'fileconfigtest');
		file_put_contents($file, $config);
		$this->_files[] = $file;
		return $file;
	}
}
