<?php

namespace Ergo\Http;

/**
 * A collection of HTTP headers
 */
class HeaderCollection implements \IteratorAggregate
{
	private $_headers=array();

	/**
	 * Constructor
	 * @param $headers HeaderField[]
	 */
	public function __construct($headers=array())
	{
		foreach($headers as $header) $this->add($header);
	}

	/**
	 * Adds a header to the collection, either in "Header: Value" format
	 * or an {@link HeaderField} object.
	 * @chainable
	 */
	function add($header)
	{
		// convert to object form
		if(is_string($header))
			$header = HeaderField::fromString($header);

		$this->_headers[] = $header;
		return $this;
	}

	/**
	 * Remove a header by name
	 * @chainable
	 */
	function remove($header)
	{
		$normalizer = new HeaderCaseNormalizer();
		$name = $normalizer->normalize($header);

		foreach($this->_headers as $idx=>$header)
		{
			if($header->getName() == $name)
				unset($this->_headers[$idx]);
		}

		return $this;
	}

	/**
	 * Replaces a header in the collection, either in "Header: Value" format
	 * or an {@link HeaderField} object.
	 * @chainable
	 */
	function replace($header)
	{
		if(is_string($header))
			$header = HeaderField::fromString($header);

		return $this
			->remove($header->getName())
			->add($header)
			;
	}

	/**
	 * Gets a single header value
	 * @return string
	 */
	function value($name, $default=false)
	{
		$values = $this->values($name);
		return count($values) ? $values[0] : $default;
	}

	/**
	 * Gets an array of the values for a header
	 * @return array
	 */
	function values($name)
	{
		$normalizer = new HeaderCaseNormalizer();
		$name = $normalizer->normalize($name);
		$values = array();

		foreach($this->_headers as $header)
		{
			if($header->getName() == $name)
			{
				$values[] = $header->getValue();
			}
		}

		return $values;
	}

	/**
	 * Returns an array of the string versions of headers
	 * @return array
	 */
	function toArray($crlf=true)
	{
		$headers = array();

		foreach($this->_headers as $header)
		{
			$string = $header->__toString();
			$headers[] = $crlf ? $string : rtrim($string);
		}

		return $headers;
	}

	/* (non-phpdoc)
	 * @see IteratorAggregate::getIterator
	 */
	function getIterator()
	{
		return new \ArrayIterator(array_values($this->_headers));
	}
}
