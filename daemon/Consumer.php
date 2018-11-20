<?php


$workerNum = 10;
$pool = new \Swoole\Process\Pool($workerNum);

$pool->on("WorkerStart", function ($pool, $workerId) {
    go(function () use ($workerId) {
        echo "Worker#{$workerId} is started\n";
        $redis = new Redis();
        $redis->connect('172.20.0.2', 6379);
        $key = 'fuck';
        $limit = 10;
        while (true) {
            $redis->sPop($key, $limit);
            $heap = $redis->scard($key);
            echo 'limit: ' . $limit, "\t", 'heap: ', $heap, "\n";
            if ($heap > 20000) {
                $limit = 300;
            } else {
                sleep(1);
            }
        }
    });


});

$pool->on("WorkerStop", function ($pool, $workerId) {
    echo "Worker#{$workerId} is stopped\n";
});

$pool->start();