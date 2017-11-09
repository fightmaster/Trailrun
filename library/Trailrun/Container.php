<?php

/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */
namespace Fightmaster\Trailrun;

use \Illuminate\Container\Container as LaravelContainer;
use Interop\Container\ContainerInterface;

class Container extends LaravelContainer implements ContainerInterface
{
    public function get($id)
    {
        return $this[$id];
    }

    public function has($id)
    {
        return isset($this[$id]);
    }
}
