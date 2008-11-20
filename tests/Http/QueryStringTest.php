<?php

/**
 */
class Ergo_Http_QueryStringTest extends UnitTestCase
{
	public function testSimpleQueryString()
	{
		$source = "key1=1&key2=".urlencode('&&&');
		$qs = new Ergo_Http_QueryString($source);

		$this->assertEqual($qs->toArray(),array('key1'=>'1','key2'=>'&&&'));
		$this->assertEqual($qs->__toString(),$source);
	}

	public function testPropertyAccess()
	{
		$source = "key1=1&key2=2";
		$qs = new Ergo_Http_QueryString($source);
		$this->assertEqual($qs->key1,1);
	}
}
