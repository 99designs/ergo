Ergo
==========

A light-weight library for processing requests and responses in PHP5.3. Provides a
closure-based infrastructure for routing, controllers and templating.

Install
-------

Ergo is designed to be easy to install and integrate.

	$ pear channel-discover pearhub.org
	$ pear install pearhub/Ergo

Alternately, check it out as a submodule and use your own classloader on the classes dir.

Use
----

	require_once('Ergo/ergo.php');

	Ergo::router()->connect('/helloworld', 'helloworld', function() {
		return Ergo::template('helloworld.tpl.php', array(
		  'greeting'=>'Hello World'
		));
	});

	Ergo::router()->connect('/*', 'any', function() {
		throw new \Ergo\Http\NotFound("Not implemented yet");
	});


Meaning
-------

From the latin, Cogito ergo sum. "I think, therefore I am".

Running the tests
-----------------

	$ ./tests/runtests.php
	runtests.php
	OK
	Test cases run: 14/14, Passes: 206, Failures: 0, Exceptions: 0

