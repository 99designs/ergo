<?php

/**
 * A factory that returns a clone of an object
 */
class Ergo_Prototype implements Ergo_Factory
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
	 * @see Ergo_Factory::create()
	 */
	public function create()
	{
		return clone $this->_object;
	}

}
