<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 19:41
 */

namespace Ndrx\Profiler\Laravel\Test;

use Ndrx\Profiler\Laravel\LaravelProfilerServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            LaravelProfilerServiceProvider::class

        ];
    }
}