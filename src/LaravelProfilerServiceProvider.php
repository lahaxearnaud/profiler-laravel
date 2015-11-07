<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:06
 */

namespace Ndrx\Profiler\Laravel;

use Illuminate\Support\Facades\Event;
use Monolog\Logger;
use Ndrx\Profiler\Collectors\Data\Context;
use Ndrx\Profiler\Collectors\Data\CpuUsage;
use Ndrx\Profiler\Collectors\Data\Duration;
use Ndrx\Profiler\Collectors\Data\Log;
use Ndrx\Profiler\Collectors\Data\PhpVersion;
use Ndrx\Profiler\Collectors\Data\Request;
use Ndrx\Profiler\Collectors\Data\Timeline;
use Ndrx\Profiler\Components\Logs\Monolog;
use Ndrx\Profiler\Laravel\Collectors\Data\Config;
use Ndrx\Profiler\Laravel\Collectors\Data\Database;
use Ndrx\Profiler\Laravel\Collectors\Data\User;
use Ndrx\Profiler\Laravel\Http\Controllers\Profiler;
use Ndrx\Profiler\NullProfiler;
use Ndrx\Profiler\ProfilerFactory;

class LaravelProfilerServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $enable = env('APP_DEBUG', false);
        /** @var Logger $logger */
        $logger = $this->app->make('log');
        try {
            $profiler = ProfilerFactory::build([
                ProfilerFactory::OPTION_ENABLE => $enable,
                ProfilerFactory::OPTION_DATASOURCE_PROFILES_FOLDER => storage_path('profiler' . DIRECTORY_SEPARATOR),
                ProfilerFactory::OPTION_COLLECTORS => [
                    PhpVersion::class,
                    CpuUsage::class,
                    Context::class,
                    Duration::class,
                    Timeline::class,
                    Request::class,
                    Log::class,
                    Duration::class,
                    Database::class,
                    Config::class,
                    Event::class,
                    User::class,
                ],

                ProfilerFactory::OPTION_LOGGER => Monolog::class
            ]);
        } catch (\Exception $e) {
            $logger->alert('Fail to build profiler error=' . $e->getMessage(), [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $profiler = new NullProfiler();
        }

        $profiler->initiate();

        if ($enable) {
            $logger->pushHandler($profiler->getLogger());
            $this->registerCors();
            $this->registerRoutes();
        }

        $this->app->bind('profiler', $profiler);
    }

    protected function registerCors ()
    {
        $this->app->register('Barryvdh\Cors\HandleCors');
    }

    protected function registerRoutes ()
    {
        $this->app->group(['middleware' => 'cors'], function () {
            $this->app->get('api/profiler/profiles', ['as' => 'profiler.profiles.list', 'uses' => Profiler::class . '@index']);
            $this->app->get('api/profiler/profiles/{id}', ['as' => 'profiler.profiles.show', 'uses' => Profiler::class . '@show']);
            $this->app->delete('api/profiler/profiles', ['as' => 'profiler.profiles.clear', 'uses' => Profiler::class . '@clear']);
        });
    }

}