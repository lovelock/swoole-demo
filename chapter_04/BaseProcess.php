<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/4
 * Time: 21:52
 */

namespace Demo\Chapter_04;


use Swoole\Process;
use Swoole\Timer;

class BaseProcess
{
    private $process;

    public function __construct()
    {
        $this->process = new Process([$this, 'run'], false, true);

        if (! $this->process->useQueue(123)) {
            var_dump(swoole_strerror(swoole_errno()));
            exit;
        }

        $this->process->start();

        while (true) {
            $data = $this->process->pop();
            echo "RECV: $data" . PHP_EOL;
        }
    }

    public function run(Process $worker)
    {
        Timer::tick(1000, function($timerId) use ($worker) {
            static $index = 0;
            ++$index;

            $worker->push('hello');

            var_dump($index);

            if ($index === 10) {
                Timer::clear($timerId);
            }
        });
    }

    public function registerSignal()
    {
        Process::signal(SIGCHLD, function($signo) {
            while ($ret = Process::wait(false)) {
                echo "PID = {$ret['PID']}\n";
            }
        });
    }
}