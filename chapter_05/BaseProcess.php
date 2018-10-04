<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/4
 * Time: 22:06
 */

namespace Demo\chapter_05;


use Swoole\Event;
use Swoole\Process;
use Swoole\Timer;

class BaseProcess
{
    /**
     * @var Process
     */
    private $process;

    /**
     * Process pool
     *
     * @var array
     */
    private $processList = [];

    /**
     * Processes in use
     *
     * @var array
     */
    private $processInUse = [];

    /**
     * minimum number of processes
     *
     * @var int
     */
    private $minNumWorkers = 3;

    /**
     * max number of processes
     * @var int
     */
    private $maxNumWorkers = 6;

    /**
     * current number of workers
     *
     * @var int
     */
    private $currentNumWorkers;

    public function __construct()
    {
        $this->process = new Process([$this, 'run'], false, true);
        $this->process->start();

        Process::wait();
    }

    public function run()
    {
        $this->currentNumWorkers = $this->minNumWorkers;

        for ($i = 0; $i < $this->currentNumWorkers; $i++) {
            echo 'initiating workers' . "\n";
            $this->spawnWorkerProcess();
        }

        Timer::tick(1000, function ($timerId) {
            echo 'number of workers now ' . $this->currentNumWorkers, "\n";
            echo 'number of workers in use ' . \count(array_filter($this->processInUse)) . "\n";

            static $index = 0;
            ++$index;
            $allProcessesBusy = true;

            foreach ($this->processInUse as $pid => $inUse) {
                if (!$inUse) {
                    $allProcessesBusy = false;
                    $this->processInUse[$pid] = 1;
                    $this->processList[$pid]->write($index . ': hello' . "\r");
                    break;
                }
            }

            if ($allProcessesBusy && $this->currentNumWorkers < $this->maxNumWorkers) {
                $pid = $this->spawnWorkerProcess();

                $this->processInUse[$pid] = 1;
                $this->processList[$pid]->write($index . ': hello' . "\r");
                $this->currentNumWorkers++;
            }

            if ($index === 10) {
                foreach ($this->processList as $pid => $process) {
                    $process->write($pid . ': exit' . "\r");
                }
                Timer::clear($timerId);
                $this->process->exit(0);
            }
        });
    }

    /**
     * spawn a worker process
     *
     * @return int
     */
    private function spawnWorkerProcess(): int
    {
        $process = new Process([$this, 'runTask'], false, true);
        $pid = $process->start();

        $this->processList[$pid] = $process;
        $this->processInUse[$pid] = 0;

        return $pid;
    }

    public function runTask(Process $worker)
    {
        Event::add($worker->pipe, function ($pipe) use ($worker) {
            $data = $worker->read();
            if ($data === 'exit' . "\r") {
                $worker->exit(0);
                exit;
            }

            echo $worker->pid . ' => ' . $data, "\n";
            $this->processInUse[$worker->pid] = 0;

            sleep(5);

            $worker->write('' . $worker->pid);
        });

        $this->registerSignal();
    }

    private function registerSignal(): void
    {
        Process::signal(SIGCHLD, function ($signo) {
            while ($ret = Process::wait(false)) {
                echo "PID = {$ret['PID']}\n";
            }
        });
    }
}