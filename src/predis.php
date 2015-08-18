<?php
require_once __DIR__ . '/../vendor/autoload.php';

function setup() {
    $parameters = [
        'tcp://127.0.0.1:7000',
        'tcp://127.0.0.1:7001',
        'tcp://127.0.0.1:7002',
        'tcp://127.0.0.1:7003',
        'tcp://127.0.0.1:7004',
        'tcp://127.0.0.1:7005',
    ];
    $options    = ['cluster' => 'redis'];

    $client = new Predis\Client($parameters, $options);
    return $client;
}
