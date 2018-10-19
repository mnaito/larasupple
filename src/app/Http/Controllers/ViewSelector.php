<?php
namespace Mits430\Larasupple\Controllers;

use Illuminate\Support\Facades\Request;

/**
 * Manages view variables
 */
trait ViewSelector
{
    /**
     * set view variable
     * @param $name
     * @param $var
     */
    protected static function set($name, $var)
    {
		$viewVariables = \Illuminate\Support\Facades\Request::get('viewVariables');
		if(!is_array($viewVariables)){
			$viewVariables = [];
		}
		
        $viewVariables[$name] = $var;
		
		$request = \Illuminate\Support\Facades\Request::instance();
		$request->attributes->add(['viewVariables' => $viewVariables]);
    }


    /**
     * select view
     * @param $template
     */
    protected static function template($template = null)
    {
        // テンプレート名が設定されていない場合、現在のコントローラーとアクションからビューを特定する
        if ($template === null) {
            // リクエストされたURLの階層通りにビューパスを作成
            $requestedPath = Request::get('requestUriWithoutExtension');
            $requestedPath = explode('/', $requestedPath);
            if (count($requestedPath) == 1) {
                $requestedPath[] = 'index';
            }

            $template = implode('/', $requestedPath);
        }
		
		$viewVariables = \Illuminate\Support\Facades\Request::get('viewVariables');
        return view($template)->with($viewVariables);
    }
}