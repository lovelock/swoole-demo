<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/2
 * Time: 23:22
 */

namespace Demo\Chapter_02;


use Swoole\Event;

class Client
{
    private $socket;

    public function __construct()
    {
        $this->socket = stream_socket_client('tcp://127.0.0.1:9501', $errno, $errstr, 30);

        Event::add($this->socket, [$this, 'onRead'], [$this, 'onWrite']);
        Event::add(STDIN, [$this, 'onInput']);
    }

    public function onRead()
    {
        // todo 这里要解决的问题是如果超过了buffersize会怎么办
        $buffer = stream_socket_recvfrom($this->socket, 1024);

        if (! $buffer) {
            echo "sever closed\n";
            Event::del($this->socket);
        }

        $unpack = json_decode($buffer, true);

        echo "\n{$unpack['from']} says: {$unpack['message']}\n";
        fwrite(STDOUT, 'EnterMsg:');
    }

    public function onWrite()
    {
        echo "onWrite\n";
    }

    public function onInput()
    {
        $msg = trim(fgets(STDIN));
        if ($msg === 'exit') {
           Event::exit();
           exit;
        }

        Event::write($this->socket, $msg);
        fwrite(STDOUT, 'Enter Msg:');
    }
}