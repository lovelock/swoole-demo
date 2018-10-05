<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/5
 * Time: 00:38
 */

namespace Demo\chapter_06;


use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class BaseServer
{
    /**
     * number of worker processes
     *
     * @var int
     */
    private $numWorker = 4;

    /**
     * number of task worker process
     *
     * @var int
     */
    private $numTaskWorker = 8;


    /**
     * @var Server
     */
    private $server;

    public function __construct()
    {
        $this->server = new Server('0.0.0.0', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_ASYNC);

        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('managerStart', [$this, 'onManagerStart']);
        $this->server->on('workerStart', [$this, 'onWorkerStart']);
//        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('start', [$this, 'onStart']);
        $this->server->set([
            'worker_num' => $this->numWorker,
//            'task_worker_num' => $this->numTaskWorker,
        ]);

        $this->server->start();
    }

    public function onStart(Server $server)
    {
        swoole_set_process_name('simple_router_master');
    }

    public function onManagerStart(Server $server)
    {
        swoole_set_process_name('simple_router_manager');
    }

    public function onWorkerStart(Server $server)
    {
        swoole_set_process_name('simple_router_worker');
    }

    public function onTask()
    {
        swoole_set_process_name('simple_router_task');
    }

    public function onRequest(Request $request, Response $response)
    {
        $path = $request->server['path_info'];
        $class = '\Demo\app\Controller' . str_replace('/', '\\', $path) . 'Controller';
        $result = (new $class())->index($request, $response);

        $response->header('content-type', 'application/json');
        $response->end(json_encode($result));
    }
}