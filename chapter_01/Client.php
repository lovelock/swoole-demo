<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/2
 * Time: 15:21
 */

namespace Demo\Chapter01;


class Client
{
    /**
     * @var \Swoole\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);

        $this->client->on('Connect', [$this, 'onConnect']);
        $this->client->on('receive', [$this, 'onReceive']);
        $this->client->on('close', [$this, 'onClose']);
        $this->client->on('error', [$this, 'onError']);

        $this->client->connect('127.0.0.1', 9501, -1);
    }

    public function onConnect(\Swoole\Client $client)
    {
        fwrite(STDOUT, "请输入消息:");

        $msg = trim(fgets(STDIN));
        $this->client->send($msg);
        $response = $this->client->recv();
        echo "get message from server: {$response}\n";
    }

    public function onReceive(\Swoole\Client $client, $data)
    {
        echo "receive: {$data}\n";
        $client->send(str_repeat('A', 100). "\n");
        sleep(1);
    }

    public function onError(\Swoole\Client $client)
    {
        echo "error\n";
    }

    public function onClose(\Swoole\Client $client)
    {
        echo "Connection close\n";
    }
}