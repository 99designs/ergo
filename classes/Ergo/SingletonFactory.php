<?php

/**
 * A factory that returns a single instance, which can be cleared
 */
interface Ergo_SingletonFactory extends Ergo_Factory
{
	/**
	 * @chainable
	 */
	public function clear();
}
