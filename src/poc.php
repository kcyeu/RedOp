<?php
define('USE_EXT', false);
define('VISUAL_MODE', FALSE);
define('USE_JSON', TRUE);
define('GEN_DATA_ON_FLY', FALSE);
//define('GEN_DATA_ON_FLY', TRUE);
define('COUNTRY_COUNT', 256);
define('SM_COUNT', 512);
//define('COUNTRY_COUNT', 2);
//define('SM_COUNT', 3);

if (USE_EXT):
require_once __DIR__ . '/ext.php';
else:
require_once __DIR__ . '/predis.php';
endif;

function generate_data() 
{
    $result = [];

    for ($i = 0; $i < SM_COUNT; $i++) {
        $sm = 'SM_' . str_pad($i, 6, '0', STR_PAD_LEFT);

        for ($j = 0; $j < COUNTRY_COUNT; $j++) {
            $country = 'C_' . str_pad($j, 6, '0', STR_PAD_LEFT);
            $result[$sm][$country] = [
                'COUNTRY'   => $country,
                'TIMESTAMP' => time()
            ];
        }
    }
    return $result;
}

function put_data($input, $redis)
{
    foreach ($input as $sm => $countries) {
        foreach ($countries as $country => $hash) {
            $redis->hmset("{$sm}:{$country}", $hash); 
        }
    }
}

function get_data($expected, $redis)
{
    foreach ($expected as $sm => $countries) {
        foreach ($countries as $country => $hash) {
            $result = $redis->hgetall("{$sm}:{$country}");
        }
    }
}

function put_json_data($input, $redis)
{
    foreach ($input as $sm => $countries) {
        $redis->set($sm, json_encode($countries)); 
    }
}

function get_json_data($expected, $redis)
{
    foreach ($expected as $sm => $countries) {
        $result = json_decode($redis->get($sm), true);
    }
}

$redis = setup();
$filename = 'data-' . SM_COUNT . '-' . COUNTRY_COUNT . '.json';

if (GEN_DATA_ON_FLY):
    $data = generate_data();
    $json_str = json_encode($data);
    file_put_contents($filename, $json_str);
else:
    $json_str = file_get_contents($filename);
    $data = json_decode($json_str, true);
endif;

$start_write_time = microtime(true);
if (USE_JSON):
    put_json_data($data, $redis);
else:
    put_data($data, $redis);
endif;
$write_time = microtime(true) - $start_write_time;

$start_read_time = microtime(true);
if (USE_JSON):
    get_json_data($data, $redis);
else:
    get_data($data, $redis);
endif;
$read_time = microtime(true) - $start_read_time;

if (USE_EXT):
$redis->close();
endif;

if (VISUAL_MODE):
    echo "Write time: {$write_time}, read time: {$read_time}" . PHP_EOL;
else:
    echo "{$write_time},{$read_time}" . PHP_EOL;
endif;

?>

