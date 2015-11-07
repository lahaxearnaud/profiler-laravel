<?php

namespace Ndrx\Profiler\Laravel\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Ndrx\Profiler\NullProfiler;
use Ndrx\Profiler\ProfilerInterface;

class Profiler extends Controller
{
    /** @var  ProfilerInterface */
    protected $profiler;

    /**
     * Profiler constructor.
     */
    public function __construct()
    {
        $this->profiler = App::make('profiler');

        if ($this->profiler instanceof NullProfiler) {
            abort(500, 'Profiler is not instantiate correctly.');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $offset = Input::get('offset', 0);
        $limit = Input::get('limit', 15);

        if ($offset < 0) {
            return response()->json(['error' => "Offset can't be lower than 0"], 400);
        }

        if ($limit < 1 || $limit > 100) {
            return response()->json(['error' => 'Limit must be between 1 and 100'], 400);
        }

        return response()->json($this->profiler->getDatasource()->all($offset, $limit));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $profile = $this->profiler->getProfile($id);

            if ($profile === null) {
                return response()->json(['error' => 'Profile not found'], 404);

            }

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        try {
            $this->profiler->getDatasource()->clear();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true]);
    }
}