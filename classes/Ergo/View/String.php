<?php

namespace Ergo\View;

/**
 * A string based view
 */
class String implements \Ergo\View
{
	private $_string;

	/**
	 * Constructor
	 */
	public function __construct($string)
	{
		$this->_string = $string;
	}

	/* (non-phpdoc)
	 * @see \Ergo\View::output()
	 */
	public function output()
	{
		return $this->_string;
	}

	/* (non-phpdoc)
	 * @see \Ergo\View::stream()
	 */
	public function stream()
	{
		$fp = fopen("php://memory", 'r+');
		fwrite($fp, $this->_string);
		rewind($fp);
		return $fp;
	}
}
