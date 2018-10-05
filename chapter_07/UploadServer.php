<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/5
 * Time: 21:44
 */

namespace Demo\chapter_07;


use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class UploadServer
{
    private $server;

    public function __construct()
    {
        $this->server = new Server('0.0.0.0', 9501);

        $this->server->on('Request', [$this, 'onRequest']);

        $this->server->start();
    }


    public function onRequest(Request $request, Response $response)
    {
        if ($request->server['request_method'] === 'GET') {
            return;
        }

        var_dump($request->files);
        $file = $request->files['file']; // 'file' 是上传文件时的key值
        $filename = $file['name'];
        $fileTmpPath = $file['tmp_name'];

        $uploadPath = __DIR__ . '/uploader/';

        if (! file_exists($uploadPath)) {
            if (!mkdir($uploadPath) && !is_dir($uploadPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadPath));
            }
        }

        move_uploaded_file($fileTmpPath, $uploadPath . $filename);

        $response->end('<h1>upload success</h1>');
    }
}