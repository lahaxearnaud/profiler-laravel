<?php

namespace Ndrx\Profiler\Laravel\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class Profiler
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $profiler = App::make('profiler');

        $response = $next($request);

        $profiler->setResponse($response);

        return $response;
    }
}
