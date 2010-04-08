Ergo
==========

A micro-library for request/response routing and HTTP interaction in PHP5. Designed to support barebones
PHP webapplication development on 99designs.com.

Example
-------

Building an Application Object

	class MyApp extends Ergo_Application
	{
		public function onStart()
		{
			// set up a central registry for core objects
			$this->registry()
				->register('db', new MyDatabaseConnection())
				->register('routes', new MyRouteMap())
				;
		}
	}

	Ergo::start(new MyApp());


Meaning
-------

The name Ergo was chosen for it's meaning "therefore". One of the core functions of the library is to provide
a central registry for applications and a way of writing applications with low coupling and easy testability.

Running the tests
-----------------

<pre><code>

$ ./tests/runtests.php
runtests.php
OK
Test cases run: 14/14, Passes: 206, Failures: 0, Exceptions: 0

</code></pre>

