<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 19:47
 */

namespace Ndrx\Profiler\Laravel\Test;


use Illuminate\Support\Facades\App;

class ApiTest extends TestCase
{
    public function createApplication()
    {
        putenv('APP_DEBUG=true');

        return parent::createApplication();
    }

    public function testAll()
    {
        $this->get('/_profiler/api/profiles');
        $this->isJson();
        $this->assertResponseOk();
    }

    public function testAllError()
    {
        $this->get('/_profiler/api/profiles?' . http_build_query([
                'offset' => -1
            ]));
        $this->isJson();
        $this->assertResponseStatus(400);

        $this->get('/_profiler/api/profiles?' . http_build_query([
                'limit' => -1
            ]));
        $this->isJson();
        $this->assertResponseStatus(400);
    }

    public function testOneNotFound()
    {
        $this->get('/_profiler/api/profiles/XXX');
        $this->isJson();
        $this->assertResponseStatus(404);
    }

    public function testOne()
    {
        /** @var \Ndrx\Profiler\Profiler $profiler */
        $profiler = App::make('profiler');
        $idProcess = $profiler->getContext()->getProcess()->getId();
        $this->get('/_profiler/api/profiles/' . $idProcess);
        $this->isJson();
        $this->assertResponseStatus(200);
    }

    public function testClear()
    {
        $this->delete('/_profiler/api/profiles');
        $this->isJson();
        $this->assertResponseOk();
    }
}