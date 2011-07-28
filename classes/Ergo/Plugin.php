<?php

namespace Ergo;

/**
 * A plugin is a component that is used to extend an {@link Application}
 * in a modular way
 */
interface Plugin
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

