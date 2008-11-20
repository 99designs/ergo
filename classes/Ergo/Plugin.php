<?php

/**
 * A plugin is a component that is used to extend an {@link Ergo_Application}
 * in a modular way
 */
interface Ergo_Plugin
{
	/**
	 * Called at the start of the application lifecycle
	 */
	public function start();

	/**
	 * Called at the very end of the application lifecycle
	 */
	public function stop();
}

