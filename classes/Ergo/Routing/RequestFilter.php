<?php

namespace Ergo\Routing;

interface RequestFilter
{
	public function filter($request);
}
