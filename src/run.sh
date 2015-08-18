#!/bin/sh

../cluster-util/cluster-util.sh flushall
php ./poc.php >> ${1}

