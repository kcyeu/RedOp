#!/bin/sh

NUM_NODES=6
REPLICAS=1

function start_nodes() {
for PORT in `ls -d 7*`
do
    cd ${PORT} && redis-server redis.conf &
done
}

function start_cluster() {
NODE_LIST=""

for PORT in `ls -d 7*`
do
    NODE_LIST="${NODE_LIST} 127.0.0.1:${PORT}"
done

redis-trib.rb create --replicas ${REPLICAS} ${NODE_LIST}
}

function stop_nodes() {
for PORT in `ls -d 7*`
do
    redis-cli -p ${PORT} shutdown
done
}

function keys() {

for PORT in 7000 7001 7002
do
    redis-cli -p ${PORT} keys "${1}"
done
}

function flushall() {

for PORT in 7000 7001 7002
do
    redis-cli -p ${PORT} flushall
done
}

case $1 in
    start)
        start_nodes
    ;;
    start-cluster)
        start_cluster
    ;;
    stop)
        stop_nodes
    ;;
    keys)
        keys "${2}"
    ;;
    flushall)
        flushall
    ;;
    *)
        echo "Usage:"
        echo "$0 [start|stop|start-cluster|flushall]"
        echo "$0 keys <key>"
    ;;
esac
