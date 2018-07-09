<?php

namespace Mits430\Larasupple\Middleware;

use Closure;

/**
 * Class RequestIDLogging
 * @package Mits430\Larasupple\Middleware
 */
class RequestIDLogging
{
    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
		
		$cookie = $request->cookie('requestID');
		if (!$cookie) {
			$cookie = str_random(40);
            $response->cookie('requestID', $cookie, 60 * 24);
        }
        \Log::debug(get_class() . $cookie);

        return $response;
    }
}
