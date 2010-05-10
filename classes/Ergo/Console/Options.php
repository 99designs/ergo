<?php

/**
 * A command line options parser
 */
class Ergo_Console_Options
{
	private $_args, $_options=array(), $_parsed;

	/**
	 * Constructor
	 * @param array $argv
	 */
	public function __construct($argv)
	{
		$this->_args = $argv;
	}

	/**
	 * Adds options definitions to the parser. Either a string or an array of strings
	 * can be provided, with keys like the following:
	 *
	 * -v
	 * --flag
	 * --flag=withvalue
	 *
	 * Bare parameters without a prefix can be captured as :alias
	 */
	public function define($options)
	{
		if(!is_array($options)) $options = array($options);

		foreach($options as $option)
		{
			$option = $this->_parseOption($option);
			$this->_options[$option->name] = $option;
		}
	}

	/**
	 * Forces a re-parse of specific arguments
	 */
	public function parse($args)
	{
		$needsValue = false;
		$argumentPattern = "/^((?:--?|:)\w+)([*?+])?(=.+?)?$/";

		foreach(array_slice($args,1) as $arg)
		{
			if($needsValue && preg_match('/^(--?|:)/', $arg))
			{
				throw new Exception("Argument {$m[1]} needs a value");
			}
			else if(preg_match('/^(--?\w+)/', $arg, $m))
			{
				if($this->_definition($m[1])->needsValue)
				{
					$needsValue = $m[1];
				}
				else
				{
					$this->_parsed[$arg][] = null;
				}
			}
			else
			{
				if($needsValue)
				{
					$this->_parsed[$needsValue][] = $arg;
					$needsValue = false;
				}
				else if($param = $this->_nextParameter())
				{
					$this->_parsed[$param][] = $arg;
				}
				else
				{
					throw new Exception("Unknown parameter $arg");
				}
			}
		}
	}

	/**
	 * Determines if the specific key has been set
	 * @return bool
	 */
	public function has($key)
	{
		if(!isset($this->_parsed)) $this->parse($this->_args);

		foreach(func_get_args() as $arg)
			if(isset($this->_parsed[$arg])) return true;

		return false;
	}

	/**
	 * Returns a single option value if set, an exception otherwise
	 * @throws Exception
	 * @return mixed
	 */
	public function value($key)
	{
		if(!isset($this->_parsed)) $this->parse($this->_args);

		if(!$this->has($key))
			throw new Exception("No value for $key");

		return $this->_parsed[$key][0];
	}

	/**
	 * Returns an array of option values
	 * @throws Exception
	 * @return array
	 */
	public function values($key)
	{
		if(!isset($this->_parsed)) $this->parse($this->_args);

		if(!$this->has($key))
			throw new Exception("No value for $key");

		return array_filter($this->_parsed[$key], array($this,'_filterNull'));
	}

	// php magic method - getter
	public function __get($property)
	{
		return $this->value($this->_key($property));
	}

	// php magic method - isset
	public function __isset($property)
	{
		return $this->has($this->_key($property));
	}

	// callback for array_filter
	private function _filterNull($value)
	{
		return !is_null($value);
	}

	// checks for a key for a property
	private function _key($property)
	{
		$candidates = array(
			"-$property", "--$property", ":$property"
			);

		foreach($candidates as $key)
			if(isset($this->_options[$key])) return $key;
	}

	// returns the internal option definition for a key
	private function _definition($key)
	{
		if(!isset($this->_options[$key]))
		{
			return (object) array(
				'name'=>$key,
				'recurrance'=>'?',
				'value'=>null,
				'needsValue'=>false,
				);
		}

		return $this->_options[$key];
	}

	// parses an options definition into a struct
	private function _parseOption($option)
	{
		if(preg_match("/^((?:--?|:)\w+)([*?+])?(=.+?)?$/",$option,$m))
		{
			return (object) array(
				'name'=>$m[1],
				'recurrance'=>empty($m[2])?'?':$m[2],
				'value'=>empty($m[3])?null:ltrim($m[3],'='),
				'needsValue'=>empty($m[3])?false:true,
				);
		}
		else
		{
			throw new InvalidArgumentException("Failed to parse $option");
		}
	}

	// returns the next bare parameter to be captured, false if none
	private function _nextParameter()
	{
		foreach($this->_options as $option)
		{
			if($option->name{0} == ':' && !isset($this->_parsed[$option->name]))
				return $option->name;
		}

		return false;
	}
}
