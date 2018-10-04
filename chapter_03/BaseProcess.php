<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/3
 * Time: 00:11
 */

namespace Demo\Chapter_03;


use Swoole\Event;
use Swoole\Process;
use Swoole\Timer;

class BaseProcess
{
    private $process;

    public function __construct()
    {
        $this->process = new Process([$this, 'run'], false, true);

        $this->process->start();

        Event::add($this->process->pipe, function ($pipe) {
            $data = $this->process->read();
            echo "RECV: " . $data . PHP_EOL;
        });

        $this->registerSignal();
    }

    public function run()
    {
        Timer::tick(1000, function ($timerId) {
            static $index = 0;
            ++$index;

            $this->process->write('hello');
            var_export($index);
            if ($index === 10) {
                Timer::clear($timerId);
            }
        });
    }

    public function registerSignal()
    {
        Process::signal(SIGCHLD, function ($singnal) {
            while ($ret = Process::wait(false)) {
                echo "PID = {$ret['pid']}\n";
            }
        });
    }
}