<?php
/**
 * Created by PhpStorm.
 * User: Frost Wong <frostwong@gmail.com>
 * Date: 2018/10/2
 * Time: 15:49
 */

namespace Demo\Chapter_01;


class TestObject
{
    public $index = 0;

    public function __construct()
    {
        echo $this->index, "\n";
    }
}