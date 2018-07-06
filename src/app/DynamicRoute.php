<?php
/**
 * Created by PhpStorm.
 * User: mnaito
 * Date: 2018/07/06
 * Time: 20:11
 */
namespace Mits430\Larasupple;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DynamicRoute
{
    public static function goDynamicRoute(Request $request, $uri)
    {
        // リクエストURIをパス文字で展開
        $pathComponents = explode('/', $uri);
        $numPathComponents = count($pathComponents);
        if ($numPathComponents == 1) {
            $pathComponents[] = 'index';
        }

        // action , controller 名を決める
        $action = array_pop($pathComponents);
        $controller = ucfirst(array_pop($pathComponents)) . 'Controller';

        // 拡張子判断
        $pathInfo = pathinfo($uri);
        $request->attributes->add(['requestPathInfo' => $pathInfo]);

        // 拡張子あり
        if (isset($pathInfo['extension'])) {
            // 拡張子を取る
            $uriWithoutExtension = substr($uri, 0, -strlen($pathInfo['extension'])-1);
            $action              = substr($action, 0, -strlen($pathInfo['extension'])-1);
        } else {
            $uriWithoutExtension = $uri;
        }

        // 後続処理で使用するために拡張子を取り除いたURIを保存
        $request->attributes->add(['requestUriWithoutExtension' => $uriWithoutExtension]);

        // 拡張子 .html なら静的ページ
        if (($pathInfo['extension'] ?? null) === 'html') {
            // アクションなし、静的ページとして表示
            return App::call(\App\Http\Controllers\RouteFallbackController::class.'@handle');
        }

        // 先頭大文字に変換してクラス名とする
        array_walk($pathComponents, function (&$item) {
            $item = ucfirst($item);
        });
        $namespacePart = implode('\\', $pathComponents);
        $fullNamespace = "\\App\\Http\\Controllers\\{$namespacePart}";
        $controllerName = "{$fullNamespace}\\{$controller}";

        // クラスの有無を得る
        try {
            $ref = new \ReflectionClass($controllerName);
            $ref->getMethod($action);
            return App::call("{$controllerName}@{$action}");
        } catch (\ReflectionException $e) {
            // アクションなし、静的ページとして表示
            return App::call(\App\Http\Controllers\RouteFallbackController::class.'@handle');
        }
    }
}