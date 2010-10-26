<?php

namespace Ergo;

/**
 * A factory used for object creation
 */
interface Factory
{
	/**
	 * @return object
	 */
	public function create();
}
