<?php

namespace Mits430\Larasupple\Middleware;

use Closure;
use Mits430\Larasupple\ViewSelector;

/**
 * Class AutoViewselect
 * @package Mits430\Larasupple\Middleware
 */
class AutoViewselect
{
    use ViewSelector;

    /**
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // レスポンスがなければビューを戻す
        $response = $next($request);
        if ($response->getOriginalContent() === null) {
            $response->setContent(self::template());
        }

        return $response;
    }
}
