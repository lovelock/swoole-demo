<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/2
 * Time: 16:59
 */

namespace Demo\Chapter_02;


class Server
{
    private $server;
    private $test;

    public function __construct()
    {
        $this->server = new \Swoole\Server('0.0.0.0', 9501);
        $this->server->set([
            'worker_num' => 1,
        ]);

        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('connect', [$this, 'onConnect']);
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->on('close', [$this, 'onClose']);

        $this->server->start();
    }

    public function onStart(\Swoole\Server $server)
    {
        echo "start\n";
    }

    public function onConnect(\Swoole\Server $server, $fd, $fromId)
    {
        echo "client {$fd} connects\n";
    }

    public function onClose(\Swoole\Server $server, $fd, $fromId)
    {
        echo "client {$fd} close connection\n";
    }

    public function onReceive(\Swoole\Server $server, $fd, $fromId, $data)
    {
        echo "get message from client {$fd}:{$data}\n";

        $pack = [
            'from' => $fd,
            'message' => $data,
        ];

        foreach ($server->connection_list() as $client) {
            if ($fd !== $client) {
                $server->send($client, json_encode($pack));
            }
        }

    }

}