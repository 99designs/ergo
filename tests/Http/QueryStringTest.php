<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

/**
 */
class QueryStringTest extends \PHPUnit_Framework_TestCase
{
	public function testSimpleQueryString()
	{
		$source = "key1=1&key2=".urlencode('&&&');
		$qs = new Http\QueryString($source);

		$this->assertEquals($qs->toArray(),array('key1'=>'1','key2'=>'&&&'));
		$this->assertEquals($qs->__toString(),$source);
	}

	public function testPropertyAccess()
	{
		$source = "key1=1&key2=2";
		$qs = new Http\QueryString($source);
		$this->assertEquals($qs->key1,1);
	}

	public function testAddParameters()
	{
		$source = "key1=1&key2=2";
		$qs = new Http\QueryString($source);
		$qs->addParameters(array('key2' => 3, 'key3' => 2));
		$this->assertEquals($qs->key2, 3);
		$this->assertEquals("key1=1&key2=3&key3=2", $qs->__toString());
	}
}
