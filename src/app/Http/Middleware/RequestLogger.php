<?php

namespace Mits430\Larasupple\Middleware;
use Closure;

/**
 * Class RequestLogger
 * @package Mits430\Larasupple\Middleware
 */
class RequestLogger
{
	public function handle($request, Closure $next, $guard = null) {
		// リクエストUUID採番
		$request = \Illuminate\Support\Facades\Request::instance();
		$requestUUID = str_random(40);
		$request->attributes->add(['requestUUID' => $requestUUID]);
		
        \Log::debug('Creating a new main Request with URI = "'. $request->getPathInfo() .'"');
        \Log::debug('Method '. $request->method() .'. parameters ' . print_r($request->query(), true));
		
		return $next($request);
	}
}