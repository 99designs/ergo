<?php

namespace Ergo\View;

use \Ergo;

/**
 * A simple php-based template
 */
class Template extends Ergo\Mixin implements Ergo\View, \ArrayAccess
{
	private $_include=array();
	private $_vars=array();
	private $_template;

	/**
	 * Assigns variables to the template
	 * @chainable
	 */
	public function assign($vars, $overwrite=true)
	{
		if(!$overwrite)
		{
			$vars = array_merge($this->_vars, $vars);
		}
		$this->_vars = $vars;
		return $this;
	}

	/**
	 * Adds one or more paths to the include dirs
	 * @chainable
	 */
	public function includePaths($paths)
	{
		$this->_include = array_merge($this->_include, (array)$paths);
		return $this;
	}

	/**
	 * Defines the template file to be used
	 * @chainable
	 */
	public function file($filename)
	{
		$this->_template = $filename;
		return $this;
	}

	/* (non-phpdoc)
	 * @see \Ergo\View::output()
	 */
	public function output()
	{
		if(!isset($this->_template))
		{
			throw new Ergo\Exception("Not template file has been assigned");
		}

		$oldInclude = get_include_path();
		set_include_path(implode(PATH_SEPARATOR, $this->_include));

		$contents = $this->_renderTemplate($this->_template, $this->_vars);

		set_include_path($oldInclude);
		return $contents;
	}

	/* (non-phpdoc)
	 * @see \Ergo\View::stream()
	 */
	public function stream()
	{
		$fp = fopen("php://memory", 'r+');
		fwrite($fp, $this->output());
		rewind($fp);
		return $fp;
	}

	/**
	 * Renders a template in the context of the current template
	 */
	public function partial($template, $vars=array())
	{
		return $this->_renderTemplate($template,
			array_merge($this->_vars, $vars));
	}

	/**
	 * Requires a template in the context of a function, with
	 * parameters extracted as php variables
	 * @see http://www.sitepoint.com/article/beyond-template-engine
	 */
	private function _renderTemplate($template, $vars)
	{
		extract($vars, EXTR_SKIP);
		ob_start();

		try
		{
			require($template);
			$contents = ob_get_contents();
		}
		catch (Exception $e)
		{
			// TODO: come up with a clever way of chaining exceptions
			ob_end_clean();
			throw $e;
		}

		ob_end_clean();
		return $contents;
	}

	// ----------------------------------------
	// SPL ArrayAccess interface

	/**
	 * @see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	 */
	public function offsetExists($offset)
	{
		return isset($this->_vars[$offset]);
	}

	/**
	 * @see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	 */
	public function offsetGet($offset)
	{
		return $this->_vars[$offset];
	}

	/**
	 * @see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
			throw new \InvalidArgumentException('Append not supported for template');

		$this->_vars[$offset] = $value;
	}

	/**
	 * @see http://www.php.net/~helly/php/ext/spl/interfaceArrayAccess.html
	 */
	public function offsetUnset($offset)
	{
		unset($this->_vars[$offset]);
	}
}
