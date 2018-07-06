<?php
namespace Mits430\Larasupple\Controller;
use Illuminate\Routing\Router;

/**
 * A RouteFallbackController
 * @package Mits430\Larasupple
 */
class RouteFallbackAbstractController extends AbstractController
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * RouteFallbackController constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param null $fallbackPlaceholder
     */
    public function handle($fallbackPlaceholder = null)
    {
        $view = null;
        try {
            // look for a view with the requested file name
            $view = view($fallbackPlaceholder);
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