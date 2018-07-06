<?php
namespace Mits430\Larasupple\Controllers;

use Illuminate\Routing\Router;
use Illuminate\Routing\Controller as BaseController;

abstract class AbstractTemplatedController extends BaseController
{
    use ViewSelector;
}