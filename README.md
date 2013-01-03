Ergo
==========

A light-weight library for processing requests and responses in PHP5.3. Provides a
closure-based infrastructure for routing, controllers and templating.

The name is from the latin, Cogito ergo sum. "I think, therefore I am".

Install
-------

Ergo is designed to be easy to install and integrate.

	$ pear channel-discover pearhub.org
	$ pear install pearhub/Ergo

Alternately, check it out as a submodule and use your own classloader on the classes dir.

Basic Usage
-----------

	require_once('Ergo/ergo.php');

	Ergo::router()->connect('/helloworld', 'helloworld', function() {
		return Ergo::template('helloworld.tpl.php', array(
		  'greeting'=>'Hello World'
		));
	});

	Ergo::router()->connect('/*', 'any', function() {
		throw new \Ergo\Http\NotFound("Not implemented yet");
	});



How to develop
-----------------

For running, Ergo has no external dependancies. For development [Composer][1] is
used to pull in SimpleTest as a dependancy.

To install dependancies via Composer:

	$ composer install --dev

Run the test suite:

	$ phpunit
	PHPUnit 3.7.10 by Sebastian Bergmann.

	Configuration read from /home/vagrant/ergo/phpunit.xml.dist

	...............................................................  63 / 100 ( 63%)
	.....................................

	Time: 0 seconds, Memory: 11.25Mb

	OK (100 tests, 269 assertions)

Status
-------

Used in several high-volume production websites, including 99designs.com, flippa.com, learnable.com and sitepoint.com.

[1]: https://github.com/composer/composer
