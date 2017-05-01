<?php
/**
 * Required Files
 * @desc Purposely included this; So it's obvious what I'm doing.
 */
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/request.php';  // A $request->query() wrapper for $_GET
require dirname(__DIR__) . '/src/client.php';   // The Elastic Client


function getCategories() {
    $es_hosts = ['localhost:9200'];
    $client = getElasticClient($es_hosts);

   $params = [
       'index' => 'inventory',
       'type' => 'category',
       'body' => [
           'query' => [
               'match_all' => new \stdClass()
           ],
       ],
        'size' => 200,
    ];

    return $client->search($params);

}

// Works
$a = getCategories();
print_r($a);



function getManufacturers() {
    $es_hosts = ['localhost:9200'];
    $client = getElasticClient($es_hosts);

   $params = [
       'index' => 'inventory',
       'type' => 'manufacturer',
       'body' => [
           'query' => [
               'match_all' => new \stdClass()
           ],
       ],
        'size' => 200,
    ];

    return $client->search($params);

}

// @TODO I have not imported all this yet, this has 3 segments to it, I only want the main MFR type, not the firearms
$a = getManufacturers();
print_r($a);


