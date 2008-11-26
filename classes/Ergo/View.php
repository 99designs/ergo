<?php

/**
 * A view represents a component which has a user-facing "view"
 */
interface Ergo_View
{
	/**
	 * Returns the contents of the view as a string
	 */
	public function output();

	/**
	 * Returns the view as a php stream
	 */
	public function stream();
}

