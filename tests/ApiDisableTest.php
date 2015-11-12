<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 19:47
 */

namespace Ndrx\Profiler\Laravel\Test;


use Illuminate\Support\Facades\App;
use Ndrx\Profiler\Context\NullContext;
use Ndrx\Profiler\Process;

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
        $this->get('/_profiler/api/profiles');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testAllError()
    {
        $this->get('/_profiler/api/profiles?' . http_build_query([
                'offset' => -1
            ]));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testOneNotFound()
    {
        $this->get('/_profiler/api/profiles/XXX');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testOne()
    {
        /** @var \Ndrx\Profiler\Profiler $profiler */
        $profiler = App::make('profiler');
        $this->assertInstanceOf(NullContext::class, $profiler->getContext());
        $this->assertInstanceOf(Process::class, $profiler->getContext()->getProcess());

        $idProcess = $profiler->getContext()->getProcess()->getId();
        $this->get('/_profiler/api/profiles/' . $idProcess);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testClear()
    {
        $this->delete('/_profiler/api/profiles');
    }
}