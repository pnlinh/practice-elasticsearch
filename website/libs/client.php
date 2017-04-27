<?php

/**
 * Quick and Sloppy way to get the Client.
 * Need it to use within a function, not writing closures either.
 *
 * You should make tihs a Factory with a re-usable instance.
 *
 * @depends elasticsearch/elasticsearch
 *           $ composer install
 */
function getElasticClient(array $hosts=[])
{
    return \Elasticsearch\ClientBuilder::create()
        ->setHosts($hosts)
        ->setRetries(2)
        ->build();
}
