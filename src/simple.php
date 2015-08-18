<?php
echo "Using extension" . PHP_EOL;
require_once __DIR__ . '/predis.php';

$client = setup();
$client->set('foo', 'bar');
echo $client->get('foo');

?>

