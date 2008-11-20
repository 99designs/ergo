<?php

class Ergo_Http_HeaderCollectionTest extends UnitTestCase
{
	public function testSimpleUsage()
	{
		$collection = new Ergo_Http_HeaderCollection();
		$collection->add('Content-Length: 9');

		$this->assertEqual($collection->values('Content-Length'), array('9'));
		$this->assertEqual($collection->value('Content-Length'), '9');
		$this->assertEqual($collection->toArray(false), array(
			'Content-Length: 9'
			));
	}

}
