<?php
namespace Mits430\Larasupple;

/**
 * Manages view variables
 */
trait ViewVariables
{
    /**
     * @var array variables for view template engine
     */
    protected $viewVariables = [];

    /**
     * set view variable
     * @param $name
     * @param $var
     */
    public function set($name, $var)
    {
        $this->viewVariables[$name] = $var;
    }
}