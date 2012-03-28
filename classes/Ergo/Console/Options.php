<?php

namespace Ergo\Console;

/**
 * A command line options parser. Takes a specification and then an array of tokens from the
 * command line to parse. Errors are captured silently for missing parameters, flags that require
 * values, etc.
 */
class Options
{
	const LOOP_LIMIT=100;

	private
		$_args,
		$_options=array(),
		$_errors=array(),
		$_parsed
		;

	/**
	 * Constructor
	 * @param array $argv
	 * @param array params to pass to define, see define()
	 */
	public function __construct($argv, $define=array())
	{
		$this->_args = $argv;
		$this->define($define);
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
	 *
	 * By default parameters can occur zero or one time, this can be controlled
	 * with characters after the flag name:
	 *
	 * --flag?	the default, zero or one
	 * --flag!	exactly one time
	 * --flag+	one or more times
	 * --flag*	zero or more times
	 *
	 * @chainable
	 */
	public function define($options)
	{
		if(!is_array($options)) $options = preg_split("/\s+/",$options);

		foreach($options as $option)
		{
			$option = $this->_parseOption($option);
			$this->_options[$option->name] = $option;
		}

		return $this;
	}

	/**
	 * Either returns the definition for a passed flag (-s or --short), or returns false
	 * @return mixed
	 */
	private function _flag($token)
	{
		if(!preg_match('/^--?([\w-]+)/', $token, $m))
			return false;

		if(!isset($this->_options[$token]))
			return false;

		return $this->_options[$token];
	}

	/**
	 * Forces a re-parse of specific arguments
	 * @chainable
	 */
	public function parse($args=null)
	{
		$args = is_null($args) ? $this->_args : $args;
		$tokens = array_slice($args,1);
		$needsValue = false;
		$i = self::LOOP_LIMIT;

		// reset global state
		$this->_parsed = array();
		$this->_errors = array();

		// process a FIFO stack of tokens
		while($token = array_shift($tokens))
		{
			// FIXME: when this is stable, remove this
			if(--$i <- 0) throw new OutOfBoundsException('Exceeded loop limit, there is a bug');

			if(!$needsValue && !isset($this->_options[$token]))
			{
				// short arguments with multiple letters need expanding
				if(preg_match('/^-([a-z0-9]{2,})$/i', $token, $m))
				{
					foreach(str_split($m[1], 1) as $x) array_unshift($tokens, "-$x");
					continue;
				}
				// joined arguments like --arg=value need splitting
				else if(preg_match('/^(--?\w+)=(.+?)$/', $token, $m))
				{
					$tokens = array_merge(explode('=', $token, 2), $tokens);
					continue;
				}
			}

			if($flag = $this->_flag($token))
			{
				if($needsValue)
					$this->_errors[] = "Flag $needsValue needs a value";

				if($flag->needsValue)
					$needsValue = $token;
				else
					$this->_parsed[$flag->name][] = NULL;
			}
			else
			{
				if($needsValue)
					$this->_parsed[$needsValue][] = $token;
				else if($param = $this->_nextParameter())
					$this->_parsed[$param][] = $token;
				else
					$this->_errors[] = "Unknown parameter $token";

				$needsValue = false;
			}
		}

		if($needsValue)
			$this->_errors[] = "Flag $needsValue needs a value";

		// check required params
		foreach($this->_options as $option=>$config)
		{
			if(in_array($config->recurrance, array('+', '!')) && !$this->has($option))
				$this->_errors[] = sprintf("Parameter $option is required");

			if(in_array($config->recurrance, array('!', '?')) && count($this->values($option)) > 1)
				$this->_errors[] = sprintf("Multiple values for $option not allowed");
		}

		return $this;
	}

	/**
	 * Returns an array of error messages related to validation, none implies valid
	 * @return array
	 */
	public function errors()
	{
		if(!isset($this->_parsed)) $this->parse($this->_args);

		return $this->_errors;
	}

	/**
	 * Prints the first error to the console
	 * @chainable
	 */
	public function printErrors()
	{
		if($errors = $this->errors())
			printf("\n%s\n", $errors[0]);

		return $this;
	}

	/**
	 * Throws exceptions for parameter validation errors
	 * @chainable
	 */
	public function validate()
	{
		foreach($this->errors() as $error)
			throw new Exception($error);

		return $this;
	}

	/**
	 * Retreives the value of the specified key, or returns the default if it
	 * doesn't exist
	 */
	public function fetch($key, $default)
	{
		return $this->has($key) ? $this->value($key) : $default;
	}

	/**
	 * Determines if the specific key has been set. If multiple parameters are passed
	 * it checks if at least one of the parameters is set.
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
		$values = $this->values($key);
		return count($values) ? $values[0] : null;
	}

	/**
	 * Returns an array of option values
	 * @throws Exception
	 * @return array
	 */
	public function values($arg)
	{
		if(!isset($this->_parsed)) $this->parse($this->_args);

		if(!isset($this->_options[$arg]) && !isset($this->_parsed[$arg]))
			throw new \InvalidArgumentException("Unknown argument $arg");

		if($this->_options[$arg]->type == 'flag' && !$this->_options[$arg]->needsValue)
		{
			return array();
		}
		else
		{
			return isset($this->_parsed[$arg])
				? $this->_parsed[$arg]
				: array($this->_options[$arg]->value)
				;
		}
	}

	// parses an options definition into a struct
	private function _parseOption($option)
	{
		if(preg_match('/^((?:--?|:)[\w-]+)([*?+!])?(=.*?)?$/',$option,$m))
		{
			return (object) array(
				'name'=>$m[1],
				'recurrance'=>empty($m[2])?'?':$m[2],
				'value'=>empty($m[3])?null:$this->_parseOptionValue(ltrim($m[3],'=')),
				'needsValue'=>empty($m[3])?false:true,
				'type'=>$option[0] == ':' ? 'param' : 'flag',
				);
		}
		else
		{
			throw new \InvalidArgumentException("Failed to parse $option");
		}
	}

	// parses values like "true" and "false" into type php var
	private function _parseOptionValue($value)
	{
		if($value === 'true')
			return true;
		if($value === '')
			return NULL;
		else if($value === 'false')
			return false;
		else if(ctype_digit($value))
			return (int) $value;
		else
			return $value;
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

	// helper to allow shorthand access
	public function __get($prop)
	{
		return $this->value($prop);
	}
}
