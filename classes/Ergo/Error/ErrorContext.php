<?php

namespace Ergo\Error;

/**
 * A class for gathering contextual information when an error occurs
 */
class ErrorContext
{
	private $_context=array();

	public function export()
	{
		$context = \Ergo::isConsole() ? $this->_consoleContext() : $this->_webContext();

		return array_merge($context, $this->_context);
	}

	public function add($key, $value)
	{
		$this->_context[$key] = $value;
	}

	public function remove($key)
	{
		unset($this->_context[$key]);
	}

	protected function _consoleContext()
	{
		$hostname = isset($_SERVER['HOSTNAME']) ? $_SERVER['HOSTNAME'] : 'unknown';
		$user = isset($_SERVER['USER']) ? $_SERVER['USER'] : 'unknown';

		return array(
			'Environment'=>'Console',
			'Host'=>$hostname,
			'User'=>$user,
			'Script'=>$_SERVER['SCRIPT_FILENAME'],
			'Working Dir'=>getcwd(),
			'Umask'=>sprintf("%04o", umask()),
		);
	}

	protected function _webContext()
	{
		$request = \Ergo::request();
		$headers = $request->getHeaders();
		$hostname = $headers->value('Host');
		$requestStr = sprintf('%s %s',
			$request->getRequestMethod(),
			$request->getUrl()
		);

		// add some metadata
		return array(
			'Environment'=>'Web',
			'Request' => $requestStr,
			'Server Name' => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'unknown',
			'Host' => $hostname,
			'Referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'unknown',
			'User IP' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown',
			'User Agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown',
		);
	}
}