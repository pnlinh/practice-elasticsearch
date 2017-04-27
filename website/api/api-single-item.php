<?php
/**
 * This is just a view for clicking an items link, the search is the harder part
 *
 * Require Autoloader
 * @depends elasticsearch/elasticsearch
 */

/**
 * Required Files
 * @desc Purposely included this; So it's obvious what I'm doing.
 */
require 'vendor/autoload.php';
require 'libs/request.php';  // A $request->query() wrapper for $_GET
require 'libs/client.php';   // The Elastic Client

// This is just a rigged $_GET method :) @ the bottom of file.
$request = new Request();

if ($product_id= $request->query('product_id')) {

    // Removed PHP7 constant incase someones on php5.
    // (Used in two places in this file)
    $es_hosts = ['localhost:9200'];

    // ** Only Get Elastic if a query is loaded up **
    $client = getElasticClient($es_hosts);

    $result = $client->get([
        'index' => 'ecommerce',
        'type' => 'product',
        'id' => $product_id,
    ]);

    $variables = [];
    $product = $result['_source'];

    // So I can use the actual variable name, varvar, lol.
    foreach ($variables as $key => $value) {
        $$key = $value;
    }
}
