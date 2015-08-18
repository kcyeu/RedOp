#!/bin/sh

../../cluster-test/run.sh flushall
php ./poc.php >> performance.log

