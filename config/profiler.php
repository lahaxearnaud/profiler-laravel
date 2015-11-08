<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 08/11/15
 * Time: 12:52
 */

return [
    'collectors' => [
        \Ndrx\Profiler\Collectors\Data\PhpVersion::class,
        \Ndrx\Profiler\Collectors\Data\CpuUsage::class,
        \Ndrx\Profiler\Collectors\Data\Context::class,
        \Ndrx\Profiler\Collectors\Data\Duration::class,
        \Ndrx\Profiler\Collectors\Data\Timeline::class,
        \Ndrx\Profiler\Collectors\Data\Request::class,
        \Ndrx\Profiler\Collectors\Data\Log::class,
        \Ndrx\Profiler\Collectors\Data\Duration::class,
        \Ndrx\Profiler\Laravel\Collectors\Data\Database::class,
        \Ndrx\Profiler\Laravel\Collectors\Data\Config::class,
        \Ndrx\Profiler\Laravel\Collectors\Data\Event::class,
        \Ndrx\Profiler\Laravel\Collectors\Data\User::class,
    ],

    'datasource' => 'file',

    'drivers' => [
        'file' => [
            'driver' => \Ndrx\Profiler\DataSources\File::class,
            'folder' => storage_path('profiler')
        ],
        'memory' => [
            'driver' => \Ndrx\Profiler\DataSources\Memory::class
        ]
    ]
];