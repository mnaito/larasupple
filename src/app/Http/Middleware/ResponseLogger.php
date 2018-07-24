<?php

namespace Mits430\Larasupple\Middleware;
use Closure;

/**
 * Class ResponseLogger
 * @package Mits430\Larasupple\Middleware
 */
class ResponseLogger
{
	public function handle($request, Closure $next)
    {
        return $next($request);
    }
	
	public function terminate($request, $response) {
		$requestType = '';
		if(preg_match("/^\/api\//", $request->getPathInfo())){
			\Log::debug('API finish. status code '. $response->status() . '. Response ' . $response->getContent());
		} else {
			\Log::debug('HTTP finish. status code '. $response->status());
		}
	}
}