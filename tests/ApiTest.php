<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 19:47
 */

namespace Ndrx\Profiler\Laravel\Test;


use Illuminate\Support\Facades\App;
use Ndrx\Profiler\Profiler;

class ApiTest extends TestCase
{

    public function testAll()
    {
        $this->get('api/profiler/profiles');
        $this->isJson();
        $this->assertResponseOk();
    }

    public function testAllError()
    {
        $this->get('api/profiler/profiles?' . http_build_query([
                'offset' => -1
            ]));
        $this->assertJson('');
        $this->assertResponseStatus(400);

        $this->get('api/profiler/profiles?' . http_build_query([
                'limit' => -1
            ]));
        $this->isJson();
        $this->assertResponseStatus(400);
    }

    public function testOneNotFound()
    {
        $this->get('api/profiler/profiles/XXX');
        $this->isJson();
        $this->assertResponseStatus(404);
    }

    public function testOne()
    {
        /** @var Profiler $profiler */
        $profiler = App::make('profiler');
        $idProcess = $profiler->getContext()->getProcess()->getId();
        $this->get('api/profiler/profiles/' . $idProcess);
        $this->isJson();
        $this->assertResponseStatus(200);
    }

    public function testClear()
    {
        $this->delete('api/profiler/profiles');
        $this->isJson();
        $this->assertResponseOk();
    }
}