<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event as EventFacade;
use Ndrx\Profiler\DataSources\Contracts\DataSourceInterface;
use Ndrx\Profiler\Events\Timeline\End;
use Ndrx\Profiler\Events\Timeline\Start;
use Ndrx\Profiler\JsonPatch;
use Ndrx\Profiler\Process;

/**
 *
 * Class Database
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Database extends \Ndrx\Profiler\Collectors\Data\Database
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
        EventFacade::listen('illuminate.query', function ($query, $bindings, $time, $connection) {
            $runnableQuery = $this->createRunnableQuery($query, $bindings, $connection);

            $queryId = uniqid();
            $currentTime = microtime(true);
            $this->process->getDispatcher()->dispatch(Start::EVENT_NAME, new Start($queryId, $runnableQuery, [], $currentTime - $time));
            $this->process->getDispatcher()->dispatch(End::EVENT_NAME, new End($queryId), $currentTime);

            $explainResults = [];
            if (preg_match('/^(SELECT) /i', $query)) {
                /** @var \PDO $pdo */
                $pdo = DB::connection($connection)->getPdo();
                $statement = $pdo->prepare('EXPLAIN ' . $query);
                $statement->execute($bindings);
                $explainResults = $statement->fetchAll(\PDO::FETCH_CLASS);
                foreach ($explainResults as $key => $value) {
                    $explainResults[$key] = (array)$value;
                }
            }

            $this->data[] = array(
                'query' => $query,
                'bindQuery' => $runnableQuery,
                'bindings' => $bindings,
                'duration' => $time,
                'connection' => $connection,
                'explain' => $explainResults
            );


            $this->stream();
        });
    }

    /**
     * Takes a query, an array of bindings and the connection as arguments, returns runnable query with upper-cased
     * keywords
     */
    protected function createRunnableQuery($query, $bindings, $connection)
    {
        // add bindings to query
        $bindings = DB::connection($connection)->prepareBindings($bindings);
        foreach ($bindings as $binding) {
            $binding = DB::connection($connection)->getPdo()->quote($binding);
            $query = preg_replace('/\?/', $binding, $query, 1);
        }

        // highlight keywords
        $keywords = ['select', 'insert', 'update', 'delete', 'where', 'from', 'limit', 'is', 'null', 'having', 'group by', 'order by', 'asc', 'desc'];
        $regexp = '/\b' . implode('\b|\b', $keywords) . '\b/i';
        $query = preg_replace_callback($regexp, function ($match) {
            return strtoupper($match[0]);
        }, $query);

        return $query;
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