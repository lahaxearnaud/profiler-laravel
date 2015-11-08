<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:06
 */

namespace Ndrx\Profiler\Laravel;

use Ndrx\Profiler\Laravel\Http\Controllers\Profiler;

class LumenProfilerServiceProvider extends LaravelProfilerServiceProvider
{

    protected function registerRoutes ()
    {

        $this->app->get('api/profiler/profiles', ['as' => 'profiler.profiles.list', 'uses' => Profiler::class . '@index']);
        $this->app->get('api/profiler/profiles/{id}', ['as' => 'profiler.profiles.show', 'uses' => Profiler::class . '@show']);
        $this->app->delete('api/profiler/profiles', ['as' => 'profiler.profiles.clear', 'uses' => Profiler::class . '@clear']);
    }
}