#!/usr/bin/env php
<?php

define('BASEDIR',dirname(__FILE__));
require_once(BASEDIR.'/classes/Ergo/ClassLoader.php');

$classloader = new Ergo_ClassLoader();
$classloader->register()->includePaths(array(
	BASEDIR."/classes",
	BASEDIR."/lib/simpletest",
	));

$options = new Ergo_Console_Options($argv, array(
	'--file=false','--dir=false','--help','-h'
	));

// show usage
if ($options->has('--help', '-h'))
{
	echo <<<EOM

CLI test runner, defaults to running Ergo tests

  --file <path>		adds a specific test file
  --dir <path>    	adds a directory containing classes/ and tests/
  --help            this documentation.

EOM;

	exit(0);
}

$testFiles = array_filter($options->values('--file'));
$dirs = array_filter($options->values('--dir'));

// default to this app's tests
if(!$options->has('--file','--dir')) $dirs[] = BASEDIR;

// add all directories
foreach($dirs as $dir)
{
	$classloader->includePaths(array(
		$dir.'/classes',
		$dir.'/tests',
		));
}

// write an include path
$classloader->export();

require_once('lib/simpletest/autorun.php');
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

