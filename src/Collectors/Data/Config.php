<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\Config as ConfigFacade;

/**
 *
 * Class User
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Config extends \Ndrx\Profiler\Collectors\Data\Config
{

    /**
     * Fetch data
     * @return void
     */
    public function resolve()
    {
        $configs = ConfigFacade::all();

        // remove DB password
        foreach ($configs['database']['connections'] as $type => $connection) {
            if (!array_key_exists('password', $connection)) {
                continue;
            }
            $configs['database']['connections'][$type]['password'] = '**********';
        }
        $configs['app']['key'] = '**********';

        $this->data = $configs;
    }
}