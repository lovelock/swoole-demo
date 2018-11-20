<?php


namespace Demo\daemon;


class Producer
{
    public function produce()
    {
        $redis = new \Redis();
        $redis->connect('172.20.0.2', 6379);
        $i = 0;

        while (true) {

            try {
//                    $redis->lPush('producer', random_bytes(2));
                $redis->sAdd('fuck', $i++);
                if ($i % 5000 === 0) {
                    echo $redis->sCard('fuck'), "\n";
//                    sleep(1);
                }
            } catch (\Exception $e) {
            }
        }
    }
}