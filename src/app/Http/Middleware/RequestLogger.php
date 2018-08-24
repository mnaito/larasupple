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
		$requestUUID = (string)\Illuminate\Support\Str::uuid();
		$request->attributes->add(['requestUUID' => $requestUUID]);
		
		// APIとWEBでログを1部分ける
		$requestType = '';
		if(preg_match("/^\/api\//", $request->getPathInfo())){
			$requestType = 'API';
		} else {
			$requestType = 'main';
		}
		
		\Log::debug('Creating a new ' . $requestType . ' Request with URI = "'. $request->getPathInfo() .'"');
		\Log::debug('Method '. $request->method() .'. parameters ' . print_r($request->all(), true));
		
		return $next($request);
	}
}