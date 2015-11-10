<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 18:06
 */

namespace Ndrx\Profiler\Laravel;

use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Monolog\Logger;
use Ndrx\Profiler\Components\Logs\Monolog;
use Ndrx\Profiler\DataSources\File;
use Ndrx\Profiler\Laravel\Http\Controllers\Profiler as ProfilerController;
use Ndrx\Profiler\NullProfiler;
use Ndrx\Profiler\ProfilerFactory;
use Ndrx\Profiler\ProfilerInterface;


class LaravelProfilerServiceProvider extends \Illuminate\Support\ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/profiler.php', config_path('profiler')
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/profiler.php', 'profiler'
        );

        if (!$this->app->bound('hash')) {
            $hash = $this->app->share(function () {
                return new BcryptHasher();
            });
            $this->app->bind('hash', $hash);
        }

        $config = $this->app->make('config');
        $enable = $config->get('app.debug');
        $enable = boolval($enable);
        $profilerRequest = $this->isProfilerCall();

        /** @var Logger $logger */
        $logger = $this->app->make('log');
        try {
            $profiler = ProfilerFactory::build($this->buildConfiguration($enable, $profilerRequest));
        } catch (\Exception $e) {
            $logger->error('Fail to build profiler error: ' . $e->getMessage(), [
                ' message : ' => $e->getMessage(),
                ' file : ' => $e->getFile(),
                ' line : ' => $e->getLine(),
                ' trace : ' => $e->getTraceAsString()
            ]);
            $profiler = new NullProfiler();
        }

        if ($enable) {
            if (!$profilerRequest) {
                $logger->getMonolog()->pushHandler($profiler->getLogger());
            }

            $this->registerCors();
            $this->registerRoutes();
        }

        $this->registerProfiler($profiler);
        $profiler->initiate();

        $profiler->getContext()->sendDebugIds();


        Event::listen('kernel.handled', function () use ($profiler) {
            $profiler->terminate();
        });
    }

    protected function isProfilerCall()
    {
        return Request::is('api/profiler/profiles*');
    }

    protected function buildConfiguration($enable, $profilerRequest)
    {
        $config = $this->app->make('config');

        $datasource = $config->get('profiler.datasource');
        $drivers = $config->get('profiler.drivers');

        $configs = [
            ProfilerFactory::OPTION_ENABLE => $enable,
            // no collector added if the request is on profiler
            ProfilerFactory::OPTION_COLLECTORS => !$profilerRequest ? $config->get('profiler.collectors') : [],
            ProfilerFactory::OPTION_DATASOURCE_CLASS => $drivers[$datasource]['driver'],
            ProfilerFactory::OPTION_LOGGER => Monolog::class
        ];

        if ($configs[ProfilerFactory::OPTION_DATASOURCE_CLASS] === File::class) {
            $configs[ProfilerFactory::OPTION_DATASOURCE_PROFILES_FOLDER] = $drivers[$datasource]['folder'];
        }

        
        return $configs;
    }

    protected function registerProfiler(ProfilerInterface $profiler)
    {
        $this->app->instance('profiler', $profiler);
    }

    protected function registerCors()
    {
        if (!headers_sent()) {
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Methods: GET, DELETE, HEAD');
        }
    }

    protected function registerRoutes()
    {
        Route::get('api/profiler/profiles', ['as' => 'profiler.profiles.list', 'uses' => ProfilerController::class . '@index']);
        Route::get('api/profiler/profiles/{id}', ['as' => 'profiler.profiles.show', 'uses' => ProfilerController::class . '@show']);
        Route::delete('api/profiler/profiles', ['as' => 'profiler.profiles.clear', 'uses' => ProfilerController::class . '@clear']);
    }

}