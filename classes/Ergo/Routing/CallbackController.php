<?php

namespace Ergo\Routing;

class CallbackController implements Controller
{
	private $_callback;

	public function __construct($callback)
	{
		$this->_callback = $callback;
	}

	/* (non-phpdoc)
	 * @see Controller::execute()
	 */
	public function execute($request)
	{
		return call_user_func($this->_callback,
			$request, new \Ergo\Http\ResponseBuilder());
	}
}
