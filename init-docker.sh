#!/bin/bash

# @description
# - Updates vm.max_map_count to recommended ElasticSearch Settings.
# - Boots Docker Images as Daemon
#
# > Elasticsearch: localhost:9200
# > Kibana: localhost:5601
# > Logstash: localhost:9300
#
# See Processes:
# > docker ps
#
# Kill Service:
# > docker kill docker_elasticsearch_1
# > docker kill docker_kibana_1
# > docker kill docker_logstash_1

echo "(+) Updating /etc/sysctl.conf vm.max_map_count=262144"

sudo sysctl -w vm.max_map_count=262144

echo "Initializing Docker Compose"

docker-compose up -d

echo "Loading, waiting 10 seconds to see status"
sleep 10

echo "(+) Docker Processes"
docker ps
