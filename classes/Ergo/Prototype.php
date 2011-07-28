<?php

namespace Ergo;

/**
 * A factory that returns a clone of an object
 */
class Prototype implements Factory
{
	private $_object;

	/**
	 * Create the prototype from the passed object
	 */
	public function __construct($object)
	{
		$this->_object = $object;
	}

	/* (non-phpdoc)
	 * @see Factory::create()
	 */
	public function create()
	{
		return clone $this->_object;
	}

}
