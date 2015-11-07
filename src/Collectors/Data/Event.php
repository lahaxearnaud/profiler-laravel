<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\Event as EventFacade;
use Ndrx\Profiler\DataSources\Contracts\DataSourceInterface;
use Ndrx\Profiler\JsonPatch;
use Ndrx\Profiler\Process;

/**
 *
 * Class Event
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Event extends \Ndrx\Profiler\Collectors\Data\Event
{
    /**
     * @param Process $process
     * @param DataSourceInterface $dataSource
     * @param JsonPatch|null $jsonPatch
     */
    public function __construct(Process $process, DataSourceInterface $dataSource, JsonPatch $jsonPatch = null)
    {
        parent::__construct($process, $dataSource, $jsonPatch);
        $this->registerListener();
    }

    /**
     *
     */
    protected function registerListener()
    {
        EventFacade::listen('*', function ($param) {
            $this->data [] = [
                'name' => EventFacade::firing(),
                'param' => json_encode($param),
                'time' => microtime(true)
            ];
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