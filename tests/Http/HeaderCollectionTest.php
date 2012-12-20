<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class HeaderCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpleUsage()
	{
		$collection = new Http\HeaderCollection();
		$collection->add('Content-Length: 9');

		$this->assertEquals($collection->values('Content-Length'), array('9'));
		$this->assertEquals($collection->value('Content-Length'), '9');
		$this->assertEquals($collection->toArray(false), array(
			'Content-Length: 9'
			));
	}

}
