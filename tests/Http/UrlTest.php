<?php

namespace Ergo\Tests\Http;

use Ergo\Http;

/**
 * @author Paul Annesley <paul@annesley.cc>
 * @licence http://www.opensource.org/licenses/mit-license.php
 * @see http://github.com/pda/phool
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
	private $_sampleData = array(
		'scheme' => 'http',
		'host' => 'example.org',
		'path' => '/path',
		'querystring' => 'a=b&c=d',
		'fragment' => 'fragment',
		'port' => 80,
	);

	public function testVerboseHttpUrlUsage()
	{
		$url = new Http\Url('http://example.org:80/path?a=b&c=d#fragment');
		$this->_assertExpectedValues($url);
		$this->assertEquals($url->getHostRelativeUrl(), '/path?a=b&c=d#fragment');
		$this->assertEquals($url->getSchemeRelativeUrl(), '//example.org/path?a=b&c=d#fragment');
	}

	public function testBriefHttpUrlUsage()
	{
		$url = new Http\Url('http://example.org/');
		$this->assertEquals($url->getHostRelativeUrl(), '/');
		$this->assertEquals($url->getSchemeRelativeUrl(), '//example.org/');
	}

	public function testHasMethods()
	{
		$url = new Http\Url('http://example.org/?query#fragment');
		$this->assertTrue($url->hasHost(), 'URL should have host');
		$this->assertTrue($url->hasScheme(), 'URL should have scheme');
		$this->assertTrue($url->hasQueryString(), 'URL should have query string');
		$this->assertTrue($url->hasFragmentString(), 'URL should have fragment string');
		$this->assertTrue($url->hasPort(), 'URL should have port');
		$this->assertTrue($url->hasDefaultPort(), 'URL should have default port');

		$url = new Http\Url('/');
		$this->assertFalse($url->hasHost(), 'URL should not have host');
		$this->assertFalse($url->hasScheme(), 'URL should not have scheme');
		$this->assertFalse($url->hasQueryString(), 'URL should not have query string');
		$this->assertFalse($url->hasFragmentString(), 'URL should not have fragment string');
		$this->assertFalse($url->hasPort(), 'URL should not have port');
		$this->assertFalse($url->hasDefaultPort(), 'URL should not have default port');
	}

	public function testExceptionsThrown()
	{
		$url = new Http\Url('/');

		try {
			$url->getScheme();
			$this->fail('getScheme() should throw exception');
		} catch (Http\UrlException $e) {
			$this->assertTrue(true);
		}

		try {
			$url->getHost();
			$this->fail('getHost() should throw exception');
		} catch (Http\UrlException $e) {
			$this->assertTrue(true);
		}

	}

	public function testBasicHttpsUsage()
	{
		$this->_assertExpectedValues(
			new Http\Url('https://example.org/path?a=b&c=d#fragment'),
			array('scheme' => 'https', 'port' => 443)
		);
	}

	public function testCustomPort()
	{
		// http
		$url = new Http\Url('http://example.org:81/path?a=b&c=d#fragment');
		$this->_assertExpectedValues($url, array('port' => 81));

		// https
		$url = new Http\Url('https://example.org:82/path?a=b&c=d#fragment');
		$this->_assertExpectedValues($url, array('port' => 82, 'scheme' => 'https'));
	}

	public function testIsDefaultPort()
	{
		$defaultPortUrls = array(
			'http://example.org/',
			'http://example.org:80/',
			'https://example.org/',
			'https://example.org:443/',
		);

		$customPortUrls = array(
			'http://example.org:443/',
			'https://example.org:80/',
			'http://example.org:8080/',
			'https://example.org:8090/',
		);

		foreach ($defaultPortUrls as $urlString)
		{
			$url = new Http\Url($urlString);
			$this->assertTrue($url->isPortDefault(),
				"port should be default in $urlString");
		}

		foreach ($customPortUrls as $urlString)
		{
			$url = new Http\Url($urlString);
			$this->assertFalse($url->isPortDefault(),
				"port should not be default in $urlString");
		}

	}

	public function testWithoutPath()
	{
		$url = new Http\Url('http://example.org');
		$this->assertEquals($url->getPath(), '/');
	}

	public function testSerializeToString()
	{
		// single element means expect in = out
		$pairs = array(

			// host-relative
			array('/'),
			array('/path'),
			array('/path/'),
			array('/path?', '/path'),
			array('/path/?', '/path/'),
			array('/path?query'),
			array('/path?query#', '/path?query'),
			array('/path?query#frag'),

			// scheme-relative
			array('//example.org', '//example.org/'),
			array('//example.org:80', '//example.org:80/'),
			array('//example.org:81', '//example.org:81/'),
			array('//example.org/'),
			array('//example.org:80/'),
			array('//example.org:81/'),
			array('//example.org/path'),
			array('//example.org/path?query'),
			array('//example.org/path?query#frag'),

			// absolute
			array('http://example.org/'),
			array('https://example.org/'),
			array('http://example.org:8000/'),
			array('https://example.org:8000/'),
			array('http://example.org:443/'),
			array('https://example.org:80/'),
			array('http://example.org:80/', 'http://example.org/'),
			array('https://example.org:443/', 'https://example.org/'),
			array('http://example.org/path?query#fragment'),
		);

		foreach ($pairs as $pair)
		{
			$in = $pair[0];
			$expect = isset($pair[1]) ? $pair[1] : $pair[0];

			$url = new Http\Url($in);
			$this->assertEquals("$url", $expect,
				"For input [$in] %s");
		}
	}

	public function testGettingUrlForASimplePath()
	{
		$url = new Http\Url('http://example.org/');
		$relative = $url->getUrlForPath('/test/path');
		$this->assertEquals($relative->__toString(), 'http://example.org/test/path');
	}

	public function testGettingUrlForPathTrimsQueryString()
	{
		$url = new Http\Url('http://example.org/my/path?test=1');
		$relative = $url->getUrlForPath('/test/path');
		$this->assertEquals($relative->__toString(), 'http://example.org/test/path');
	}

	public function testGetUrlForRelativePath()
	{
		$url = new Http\Url('http://example.org/my');
		$relative = $url->getUrlForRelativePath('/path');
		$this->assertEquals($relative->__toString(),
			'http://example.org/my/path');
	}

	public function testGetForRelativePathWithTrailingSlash()
	{
		$url = new Http\Url('http://example.org/my/path/?test=1');
		$relative = $url->getUrlForRelativePath('/sub/path/');
		$this->assertEquals($relative->__toString(),
			'http://example.org/my/path/sub/path/');
	}

	public function testGetForRelativePathWithNoLeadingSlash()
	{
		$url = new Http\Url('http://example.org/my/path?test=1');
		$relative = $url->getUrlForRelativePath('sub/path/');
		$this->assertEquals($relative->__toString(),
			'http://example.org/my/path/sub/path/');
	}

	public function testGetForRelativePathWithOnlyQueryString()
	{
		$url = new Http\Url('http://example.org/test');
		$relative = $url->getUrlForRelativePath('?blargh=1');
		$this->assertEquals($relative->__toString(),
			'http://example.org/test?blargh=1');
	}

	public function testGetForRelativePathWithOnlySlash()
	{
		$url = new Http\Url('http://example.org/test');
		$relative = $url->getUrlForRelativePath('/');
		$this->assertEquals($relative->__toString(),
			'http://example.org/test');
	}

	public function testGetUrlForParameters()
	{
		$url = new Http\Url('http://example.org/test');
		$relative = $url->getUrlForParameters(array('a'=>1,'b'=>2,'c'=>'test'));
		$this->assertEquals($relative->__toString(),
			'http://example.org/test?a=1&b=2&c=test');
	}

	public function testGetUrlForScheme()
	{
		// implicit default port
		$url = new Http\Url('http://example.org');
		$this->assertEquals('https://example.org/', (string)$url->getUrlForScheme('https'));

		// explicit default port
		$url = new Http\Url('http://example.org:80');
		$this->assertEquals('https://example.org/', (string)$url->getUrlForScheme('https'));

		// explicit non standard port
		$url = new Http\Url('http://example.org:123');
		$this->assertEquals('https://example.org:123/', (string)$url->getUrlForScheme('https'));
	}

	public function testGetUrlForFragment()
	{
		// url without a fragment currently
		$url = new Http\Url('http://example.org/');
		$this->assertEquals('http://example.org/#blarg', (string)$url->getUrlForFragment('blarg'));

		// url with a fragment loses current fragment
		$url = new Http\Url('http://example.org/#blarg');
		$this->assertEquals('http://example.org/#gralb', (string)$url->getUrlForFragment('gralb'));
	}

	public function testGetUrlForMergedParameters()
	{
		// url without a fragment currently
		$url = new Http\Url('http://example.org/?key1=val1&key2=val2&key3=&');
		$newurl = $url->getUrlForMergedParameters(array(
			"key2" => "XXX",
			"key4" => "val4")
		);
		$this->assertEquals('http://example.org/?key1=val1&key2=XXX&key3=&key4=val4', (string)$newurl);
	}

	public function testGetUrlForMergedParametersWithEmptyQuery()
	{
		// url without a fragment currently
		$url = new Http\Url('http://example.org/');
		$newurl = $url->getUrlForMergedParameters(array(
			"key2" => "XXX",
			"key4" => "val4")
		);
		$this->assertEquals('http://example.org/?key2=XXX&key4=val4', (string)$newurl);
	}



	// ----------------------------------------

	private function _assertExpectedValues($url, $custom = array())
	{
		$expected = array_merge($this->_sampleData, $custom);

		$this->assertEquals($url->getScheme(), $expected['scheme']);
		$this->assertEquals($url->getHost(), $expected['host']);
		$this->assertEquals($url->getPath(), $expected['path']);
		$this->assertEquals($url->getQueryString(), $expected['querystring']);
		$this->assertEquals($url->getFragmentString(), $expected['fragment']);
		$this->assertEquals($url->getPort(), $expected['port']);
	}

}
