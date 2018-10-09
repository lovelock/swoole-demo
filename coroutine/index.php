<?php


$socket = new Co\Socket(AF_INET, SOCK_STREAM, 0);
$socket->bind('127.0.0.1', 2234);
$socket->listen(128);

go(function () use ($socket) {
    while (true) {
        echo "Accept: \n";
        $client = $socket->accept();
        if ($client === false) {
            var_dump($socket->errCode);
        } else {
            go(function () use ($socket, $client) {

                while (true) {
                    $buffer = $client->recv();
                    if ($buffer) {
                        echo $buffer;
                        $ret = $client->send($buffer);
                        if (!$ret) {
                            echo "client closed in send\n";
                            $client->close();
                            break;
                        }
                    } else {
                        echo "client closed\n";
                        $client->close();
                        break;
                    }

                }

            });

        }

    }
});