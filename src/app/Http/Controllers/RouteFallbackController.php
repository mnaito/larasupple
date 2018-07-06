<?php
namespace Mits430\Larasupple\Controllers;
use Illuminate\Support\Facades\Request;

/**
 * A RouteFallbackController
 * @package Mits430\Larasupple
 */
class RouteFallbackController extends AbstractController
{
    /**
     * @param null $fallbackPlaceholder
     */
    public function handle($fallbackPlaceholder = null)
    {
        $view = null;
        try {
            // look for a view with the requested file name
            $view = view(Request::get('requestUriWithoutExtension'));
        }
        // Laravel throws this exception when no matching view is found inside
        catch (\InvalidArgumentException $e) {
            abort(404);
        }
        catch (\Exception $e) {
            throw $e;
        }

        return $this->after($view);
    }


    /**
     * @param \Illuminate\View\View $view
     */
    protected function after(\Illuminate\View\View $view)
    {
        return $view;
    }
}