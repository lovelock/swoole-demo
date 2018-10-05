<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/5
 * Time: 01:23
 */

namespace Demo\app\Controller\Module1;


use Swoole\Http\Request;
use Swoole\Http\Response;

class HelloController
{
    public function index(Request $request, Response $response)
    {
        $args = $request->get;
        $page = (int)$args['page'];
        $pageSize = (int)$args['page_size'];

        return [
            'hello' => 'world',
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }
}