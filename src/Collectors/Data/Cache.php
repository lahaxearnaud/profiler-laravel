<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\Event;

/**
 *
 * Class Cache
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Cache extends \Ndrx\Profiler\Collectors\Data\Cache
{

    protected function registerListeners()
    {
        Event::listen('cache.hit', function ($key, $value) {
            $this->data[] = [
                'action' => 'get',
                'success' => true,
                'result' => $value,
                'key' => $key
            ];
            $this->stream();
        });

        Event::listen('cache.missed', function ($key) {
            $this->data[] = [
                'action' => 'get',
                'success' => false,
                'result' => null,
                'key' => $key
            ];
            $this->stream();
        });

        Event::listen('cache.write', function ($key, $value, $minutes) {
            $this->data[] = [
                'action' => 'put',
                'value' => $value,
                'lifetime' => $minutes * 60,
                'key' => $key
            ];
            $this->stream();
        });

        Event::listen('cache.delete', function ($key) {
            $this->data[] = [
                'action' => 'delete',
                'key' => $key
            ];
            $this->stream();
        });
    }
}