<?php
namespace Mits430\Larasupple;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

/**
 * Debug Controller
 * @author mnaito
 */
class DebugController extends Controller
{
    use ViewVariables;

    /**
     * @var Router
     */
    protected $router;

    /**
     * DebugController constructor.
     * @param Router $router injected from Laravel
     */
    public function __construct(Router $router)
    {
        $this->router = $router;

        // decline this request if APP_ENV is either local or dev
        if (!App::environment(['local', 'dev'])) {
            abort(404);
        }
    }

    /**
     * List all implemented debug actions
     */
    public function index()
    {
        // discovered debug actions
        $debugActions = [];

        $reflect = new \ReflectionClass(get_class($this->router->current()->controller));

        // Ensure we only pull out the public methods
        $methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);

        sort($methods);

        if (count($methods) > 0) {
            foreach ($methods as $method) {
                // treat all methods whose belonging namespace App\Http\Controllers as controller action
                if (strpos($method->class, 'App\Http\Controllers\DebugController') !== 0)
                    continue;

                // generate array
                $comment = self::extractComment($method->getDocComment());
                $debugActions[] = array(
                    'action' => $method->name,
                    'comment' => $comment,
                );
            }
        }

        //TODO: m.naito - needed to improve more
        dump($debugActions);
        $this->set('debugActions', $debugActions);

        //TODO: m.naito - not yet supported
        /*
        // discover APIs
        $apis= array();
        $files = \Finder::instance()->list_files('classes/api');

        sort($files);

        if (count($files) > 0)
        {
            foreach ($files as $file)
            {
                $task_name = str_replace('.php', '', basename($file));
                $class_name = 'Api_'.$task_name;

                require_once $file;

                $reflect = new \ReflectionClass($class_name);

                if ($reflect->isAbstract())
                    continue;

                // Ensure we only pull out the public methods
                $methods = $reflect->getMethods(\ReflectionMethod::IS_PUBLIC);

                sort($methods);

                $apis[$reflect->name] = array(
                    'class' => $task_name,
                    'actualClass' => $reflect->name,
                    'actions' => array(),
                );

                if (count($methods) > 0)
                {
                    foreach ($methods as $method)
                    {
                        $comment = self::extractComment($method->getDocComment());
                        strpos($method->name, 'action_') === 0 and $apis[$reflect->name]['actions'][] = array(
                            'name' => str_replace('action_', '', $method->name),
                            'actualName' => $method->name,
                            'comment' => $comment,
                        );
                    }
                }
            }

            //$this->set('apis', $apis);

            //$this->set_smarty_nolang("debug/index");
        }*/
    }


    /**
     * Extract PHPDoc comment
     * @param $comment
     * @return mixed|null|string|string[]
     */
    private static function extractComment($comment)
    {
        $comment = preg_replace('/^\s*\/\*\*|\s*\*\/$/', '', $comment);
        $comment = array_reduce(explode("\n", $comment), function ($carry, $item) {
            $item = preg_replace('/^\s*\*\s*/', '', $item) . "\n";
            $item = preg_replace('/\/$/', '', $item);
            $carry .= preg_replace('/^\s*/', '', $item);
            return $carry;
        }, '');

        return $comment;
    }
}