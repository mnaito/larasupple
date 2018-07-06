<?php
namespace Mits430\Larasupple\Controller;

use Illuminate\Routing\Router;
use Illuminate\Routing\Controller as BaseController;

abstract class AbstractTemplatedController extends BaseController
{
    use ViewSelector;
}