<?php
/**
 * User: qingchun3
 * Date: 2018/10/8
 */

namespace Demo\chapter_08;



use Redis;
use Swoole\Process\Pool;

class BaseSwooleProcessPool
{
    private $pool;

    public function __construct()
    {
        $this->pool = new Pool(10);

        $this->pool->on('workerStart', [$this, 'onWorkerStart']);
        $this->pool->on('workerStop', [$this, 'onWorkerStop']);

        $this->pool->start();
    }

    public function onWorkerStart(Pool $pool, $workerId)
    {
        echo "#{$workerId} started\n";
        $redis = new Redis();
        $redis->pconnect('swoole-demo_redis_1', 6379);
        $key = 'key1';

        while (true) {
//            $redis->lPush($key, random_int(100, 999999));
            echo $redis->rPop($key);
        }
    }

    public function onWorkerStop(Pool $pool, $workerId)
    {
        echo "#{$workerId} has stopped\n";
    }
}