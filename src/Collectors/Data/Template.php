<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Support\Facades\Event as EventFacade;;
use Illuminate\View\View;

/**
 *
 * Class Database
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class Template extends \Ndrx\Profiler\Collectors\Data\Template
{

    protected function registerListeners()
    {
        EventFacade::listen('composing: *', function (View $view) {

            $data = array_except($view->getData(), ['obLevel', '__env', 'app']);
            $this->data[] = [
                'data' => $data,
                'file' => $view->getPath(),
                'name' => $view->name(),
                'time' => microtime(true)
            ];

            $this->stream();
        });
    }
}