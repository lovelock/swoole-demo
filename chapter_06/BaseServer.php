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

        $this->server->start();
    }

    public function onRequest(Request $request, Response $response)
    {
        var_dump($request->get);
        var_dump($request->post);
        var_dump($request->cookie);
        var_dump($request->server);
        var_dump($request->files);
        var_dump($request->header);

        $response->cookie('x-www', 'fuck');
        $response->header('x-server', 'kdjdskfjks');
        $response->end('<h1>hello</h1>');
    }
}