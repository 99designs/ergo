<?php

namespace Ergo\Tests\Routing;

use \Ergo\Http;

\Mock::generate('\Ergo\Routing\Controller', 'MockController');

class FilteredControllerTest extends \UnitTestCase
{
	public function testRequestFilters()
	{
		$delegate = new \Ergo\Routing\CallbackController(function($request, $builder){
			return $builder
				->setBody($request->getUrl())
				->build();
		});

		$filtered = new \Ergo\Routing\FilteredController($delegate);
		$filtered->addRequestFilter(function($request) {
			return new Http\Request('GET','/user/blargh');
		});

		$response = $filtered->execute(new Http\Request('GET','/some/url'));
		$this->assertEqual($response->getBody(), '/user/blargh');
	}

	public function testResponseFilters()
	{
		$delegate = new \MockController();
		$delegate->setReturnReference('execute', new Http\Response(200, array(), 'left blank'));

		$filtered = new \Ergo\Routing\FilteredController($delegate);
		$filtered->addResponseFilter(function($response) {
			return new Http\Response(200, array(), $response->getBody() . ', blargh');
		});

		$response = $filtered->execute(new Http\Request('GET','/some/url'));
		$this->assertEqual($response->getBody(), 'left blank, blargh');
	}
}
