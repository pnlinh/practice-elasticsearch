<?php

require 'vendor/autoload.php';

$elastic = ClientBuilder::create()
->setHosts('localhost')
    ->setRetries(2)
    ->build();
$query = isset($_POST['query']) ?? false;


$params = [
    'index' => 'ecommerce',
    'type' => 'product',
    'body' => [
        'query' => [
            'match' => [
                'name' => $query
            ]
        ]
    ],
];

$elastic->search($params);

function output($data, $error = false) {
    return json_encode([
        'data' => $data,
        'error' => ($error) ?? false
    ]);
}
