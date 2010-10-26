<?php

namespace Ergo;

/**
 * A factory that returns a single instance, which can be cleared
 */
interface SingletonFactory extends Factory
{
	/**
	 * @chainable
	 */
	public function clear();
}
