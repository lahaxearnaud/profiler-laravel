<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 19:47
 */

namespace Ndrx\Profiler\Laravel\Test;


use Illuminate\Support\Facades\App;

class ApiDisableTest extends TestCase
{
    public function createApplication()
    {
        putenv('APP_DEBUG=false');

        return parent::createApplication();
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testAll()
    {
        $this->get('api/profiler/profiles');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testAllError()
    {
        $this->get('api/profiler/profiles?' . http_build_query([
                'offset' => -1
            ]));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testOneNotFound()
    {
        $this->get('api/profiler/profiles/XXX');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testOne()
    {
        /** @var \Ndrx\Profiler\Profiler $profiler */
        $profiler = App::make('profiler');
        $idProcess = $profiler->getContext()->getProcess()->getId();
        $this->get('api/profiler/profiles/' . $idProcess);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testClear()
    {
        $this->delete('api/profiler/profiles');
    }
}