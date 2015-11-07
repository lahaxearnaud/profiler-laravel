<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:25
 */

namespace Ndrx\Profiler\Laravel;


use Illuminate\Support\Facades\Facade;

class ProfilerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'profiler';
    }
}