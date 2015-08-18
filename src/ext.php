<?php

define('USE_REDIS_CLUSTER', true);

function setup() {
if (USE_REDIS_CLUSTER):
    $redis = new RedisCluster(
        'cluster1',
        [
            '127.0.0.1:7000',
            '127.0.0.1:7001',
            '127.0.0.1:7002',
        ]
    );
else:
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
endif;
    return $redis;
}
