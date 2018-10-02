<?php

namespace Demo\Chapter01;

class Server
{
    private $server;

    public function __construct()
    {
        $this->server = new \Swoole\Server('0.0.0.0', 9501);
        $this->server->set(
            [
                'worker_num' => 8,
                'daemonize' => false,
                'max_requests' => 10000,
                'dispatch_mode' => 2,
                'task_worker_num' => 8,
            ]
        );
        $this->server->on('Start', [$this, 'onStart']);
        $this->server->on('Connect', [$this, 'onConnect']);
        $this->server->on('Close', [$this, 'onClose']);
        $this->server->on('Receive', [$this, 'onReceive']);
        $this->server->on('Task', [$this, 'onTask']);
        $this->server->on('Finish', [$this, 'onFinish']);

        $this->server->start();
    }

    public function onStart()
    {
        echo "start \n";
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

        $data = [
          'task' => 'task_1',
          'params' => $data,
          'fd' => $fd,
        ];

        $server->task(json_encode($data));
    }

    public function onTask(\Swoole\Server $server, $taskId, $fromId, $data)
    {
        echo "this task {$taskId} from worker {$fromId}\n";
        echo "data: {$data}\n";

        $data = json_decode($data, true);

        echo "receive task: {$data['task']}\n";

        $server->send($data['fd'], 'hello task');
        return 'Finished';
    }

    public function onFinish(\Swoole\Server $server, $taskId, $data)
    {
        echo "task {$taskId} finish\n";

        echo "result: {$data}\n";
    }
}
