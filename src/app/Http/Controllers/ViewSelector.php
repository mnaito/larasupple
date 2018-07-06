<?php
namespace Mits430\Larasupple\Controller;

use Illuminate\Support\Facades\Request;

/**
 * Manages view variables
 */
trait ViewSelector
{
    /**
     * @var array variables for view template engine
     */
    protected static $viewVariables = [];

    /**
     * set view variable
     * @param $name
     * @param $var
     */
    protected static function set($name, $var)
    {
        self::$viewVariables[$name] = $var;
    }


    /**
     * select view
     * @param $template
     */
    protected static function template($template = null)
    {
        // テンプレート名が設定されていない場合、現在のコントローラーとアクションからビューを特定する
        if ($template === null) {
            $request = Request::instance();

            // リクエストされたURLの階層通りにビューパスを作成
            $requestedPath = $request->path();
            $requestedPath = explode('/', $requestedPath);
            if (count($requestedPath) == 1) {
                $requestedPath[] = 'index';
            }

            $template = implode('/', $requestedPath);
        }

        return view($template)->with(self::$viewVariables);
    }
}