<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:06
 */

namespace Ndrx\Profiler\Laravel;

class LumenProfilerServiceProvider extends LaravelProfilerServiceProvider
{

    protected function registerCors ()
    {
        $this->app->register('Barryvdh\Cors\LumenServiceProvider');
    }
}