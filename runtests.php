#!/usr/bin/env php
<?php

define('BASEDIR',dirname(__FILE__));
require_once(BASEDIR.'/classes/Ergo/ClassLoader.php');

// show
if (in_array('--help', $argv))
{
	echo <<<EOM

CLI test runner.

Available options:

  --file <path>		adds a specific test file
  --dir <path>    	adds a directory containing classes/ and tests/
  --help            this documentation.

EOM;

	exit(0);
}

$testFiles = array();
$dirs = array();

// collect arguments
for($i=1; $i<count($argv); $i++)
{
	if($argv[$i]=='--file') $testFiles[] = $argv[++$i];
	if($argv[$i]=='--dir') $dirs[] = $argv[++$i];
}

// default to this app's tests
if(count($dirs)==0 && count($files)==0) $dirs[] = BASEDIR;

// build class loader
$classloader = new Ergo_ClassLoader();
$classloader->register()
	->includePaths(array(
		"$basedir/lib/simpletest",
		));

// add all directories
foreach($dirs as $dir)
{
	$classloader->includePaths(array(
		$dir.'/classes',
		$dir.'/tests',
		));
}

$classloader->export();

require_once('autorun.php');

$suite = new TestSuite('Tests');

if ($testFiles)
{
	foreach($testFiles as $test)
	{
		$suite->addFile($test);
	}
}
else
{
	foreach($dirs as $dir)
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($dir.'/tests'));

		foreach ($iterator as $file)
		{
			if(preg_match('/(UseCase|Test).php$/',$file->getFileName()))
			{
				$suite->addFile($file->getPathname());
			}
		}
	}
}

