<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\Facades\App;

/**
 *
 * Class User
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Route extends \Ndrx\Profiler\Collectors\Data\Route
{

    /**
     * Fetch data
     * @return void
     */
    public function resolve()
    {
        $routes = App::make('router')->getRoutes();
        /** @var \Illuminate\Routing\Route $route */
        foreach ($routes as $route) {
            $middleware = array_unique(array_values($route->middleware()));

            $this->data[] = array(
                'host'   => $route->domain(),
                'method' => implode('|', $route->methods()),
                'uri'    => $route->uri(),
                'name'   => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $middleware,
            );
        }
    }
}