<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\Event as EventFacade;
use Ndrx\Profiler\JsonPatch;

/**
 *
 * Class Event
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Event extends \Ndrx\Profiler\Collectors\Data\Event
{
    /**
     *
     */
    protected function registerListeners()
    {
        EventFacade::listen('*', function ($param) {
            $this->data [] = [
                'name' => EventFacade::firing(),
                'param' => $param,
                'time' => microtime(true)
            ];

            $this->stream();
        });
    }

    /**
     * Write data in the datasource and clean current buffer
     * @return mixed
     */
    public function stream()
    {
        $patch = $this->jsonPatch->generate($this->getPath(), JsonPatch::ACTION_ADD, $this->data, true);
        $this->dataSource->save($this->process, [$patch]);
        $this->data = [];
    }
}