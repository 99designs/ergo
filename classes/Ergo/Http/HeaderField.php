<?php

namespace Ergo\Http;

/**
 * An HTTP header belonging to a Request or a Response.
 *
 * Specifically, one of:
 *   General Header {@link http://tools.ietf.org/html/rfc2616#section-4.5}
 *   Request Header {@link http://tools.ietf.org/html/rfc2616#section-5.3}
 *   Response Header {@link http://tools.ietf.org/html/rfc2616#section-6.2}
 *   Entity Header {http://tools.ietf.org/html/rfc2616#section-7.1}
 *
 * All follow the general format given in Section 3.1 of RFC 822.
 * {@link http://tools.ietf.org/html/rfc822#section-3.1}
 *
 * @author Paul Annesley <paul@annesley.cc>
 * @licence http://www.opensource.org/licenses/mit-license.php
 * @see http://github.com/pda/phool
 */
class HeaderField
{
	const CRLF = "\r\n";

	private $_name;
	private $_value;

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function __construct($name, $value)
	{
		$normalizer = new HeaderCaseNormalizer();
		$this->_name = $normalizer->normalize($name);
		$this->_value = $value;
	}

	/**
	 * The case-normalized name of the header.
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * The value of the header.
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * The full header string, e.g. 'Example-Header: Some Value'
	 * @return string
	 */
	public function __toString()
	{
		return sprintf(
			'%s: %s%s',
			$this->getName(),
			$this->getValue(),
			self::CRLF
		);
	}

	/**
	 * Creates a header from a string representing a single header.
	 * @param string $headerString
	 * @return
	 */
	public static function fromString($headerString)
	{
		$headerString = trim($headerString);
		list($name, $value) = explode(': ', trim($headerString), 2);
		return new self($name, $value);
	}

}
