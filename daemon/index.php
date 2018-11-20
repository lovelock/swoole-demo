<?php

require_once __DIR__ . '/../vendor/autoload.php';

$producer = new \Demo\daemon\Producer();
$producer->produce();