<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

class HeaderCollectionTest extends \UnitTestCase
{
	public function testSimpleUsage()
	{
		$collection = new Http\HeaderCollection();
		$collection->add('Content-Length: 9');

		$this->assertEqual($collection->values('Content-Length'), array('9'));
		$this->assertEqual($collection->value('Content-Length'), '9');
		$this->assertEqual($collection->toArray(false), array(
			'Content-Length: 9'
			));
	}

}
