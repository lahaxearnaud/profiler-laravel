<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:06
 */

namespace Ndrx\Profiler\Laravel;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Route;
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
use Ndrx\Profiler\Laravel\Collectors\Data\Event;
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
        $this->app['hash'] = $this->app->share(function () {
            return new BcryptHasher();
        });
        $enable = 1 == env('APP_DEBUG', false);
        /** @var Logger $logger */
        $logger = $this->app->make('log');
        try {
            $profiler = ProfilerFactory::build([
                ProfilerFactory::OPTION_ENABLE => $enable,
                ProfilerFactory::OPTION_DATASOURCE_PROFILES_FOLDER => '/tmp/profiler/',
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

        if ($enable) {
            $logger->getMonolog()->pushHandler($profiler->getLogger());
            $this->registerCors();
            $this->registerRoutes();
        }

        $profiler->initiate();

        $this->app->instance('profiler', $profiler);
    }

    protected function registerCors()
    {
        if (!headers_sent()) {
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH');
        }
    }

    protected function registerRoutes()
    {
        Route::get('api/profiler/profiles', ['as' => 'profiler.profiles.list', 'uses' => Profiler::class . '@index']);
        Route::get('api/profiler/profiles/{id}', ['as' => 'profiler.profiles.show', 'uses' => Profiler::class . '@show']);
        Route::delete('api/profiler/profiles', ['as' => 'profiler.profiles.clear', 'uses' => Profiler::class . '@clear']);
    }

}