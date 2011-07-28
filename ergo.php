<?php

use \Ergo;
use \Ergo\ClassLoader;
use \Ergo\Application;

// set up the class loader environment
require_once(__DIR__."/classes/Ergo/ClassLoader.php");

$classloader = new ClassLoader();
$classloader->register(array(__DIR__."/classes"));

// start a default application
Ergo::start(new Application($classloader));

