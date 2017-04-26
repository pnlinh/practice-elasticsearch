<?php
require 'vendor/autoload.php';

/**
 * Quick and Sloppy way to get the Client.
 * Need it to use within a function, not writing closures either.
 */
function getClient()
{
    return Elasticsearch\ClientBuilder::create()
        ->setHosts(['localhost:9200'])
        ->setRetries(2)
        ->build();
}

$client = getClient();

define('RESULTS_PER_PAGE', 5);

$variables = [];

$request = new class() {
      function query($param) {
          return isset($_GET[$param]) ? trim($_GET[$param]) : false;
      }
};


if ($query = $request->query('query')) {
    $query = trim($query);
    $page = $request->query('page', 1);
    $from = (($page - 1) * RESULTS_PER_PAGE);

    $variables['page'] = $page;
    $variables['from'] = $from;
    $variables['query'] = $query;

    $queryArray = [
        'bool' => [
            'must' => [],
            'filter' => [],
        ],
    ];
    $tokens = explode(' ', $query);

    foreach ($tokens as $token) {
        $queryArray['bool']['must'][] = [
            'match' => [
                'name' => [
                    'query' => $token,
                    'fuzziness' => 'AUTO',
                ],
            ],
        ];
    }

    $variables['aggregations'] = getSearchFilterAggregations($queryArray);

    /* Filters */
    $startPrice = $request->query('startprice');
    $endPrice = $request->query('endprice');
    $status = $request->query('status');
    $category = $request->query('category');

    $variables['startPrice'] = $startPrice;
    $variables['endPrice'] = $endPrice;
    $variables['status'] = $status;
    $variables['category'] = $category;

    // Price
    if ($startPrice && $endPrice) {
        $queryArray['bool']['filter'][] = [
            'range' => [
                'price' => [
                    'gte' => $startPrice,
                    'lte' => $endPrice,
                ],
            ],
        ];
    }

    // Status
    if ($status) {
        $queryArray['bool']['filter'][] = [
            'term' => [
                'status' => $status,
            ],
        ];
    }

    // Category
    if ($category) {
        $queryArray['bool']['filter'][] = [
            'nested' => [
                'path' => 'categories',
                'query' => [
                    'term' => [
                        'categories.name' => $category,
                    ],
                ],
            ],
        ];
    }

    $params = [
        'index' => 'ecommerce',
        'type' => 'product',
        'body' => [
            'query' => $queryArray,
            'size' => RESULTS_PER_PAGE,
            'from' => $from,
        ],
    ];

    $result = $client->search($params);
    $total = $result['hits']['total'];
    $variables['total'] = $total;

    $to = ($page * RESULTS_PER_PAGE);
    $to = ($to > $total ? $total : $to);
    $variables['to'] = $to;

    if (isset($result['hits']['hits'])) {
        $variables['hits'] = $result['hits']['hits'];
    }
}


function getSearchFilterAggregations(array $queryArray)
{
    $client = getClient();

    $params = [
        'index' => 'ecommerce',
        'type' => 'product',
        'body' => [
            'query' => $queryArray,
            'size' => 0,
            'aggs' => [
                'statuses' => [
                    'terms' => [ 'field' => 'status' ]
                ],

                'price_ranges' => [
                    'range' => [
                        'field' => 'price',
                        'ranges' => [
                            [ 'from' => 1, 'to' => 25 ],
                            [ 'from' => 25, 'to' => 50 ],
                            [ 'from' => 50, 'to' => 75 ],
                            [ 'from' => 75, 'to' => 100 ]
                        ],
                    ],
                ],

                'categories' => [
                    'nested' => [
                        'path' => 'categories',
                    ],
                    'aggs' => [
                        'categories_count' => [
                            'terms' => [ 'field' => 'categories.name' ]
                        ],

                    ],
                ],


            ],
        ],
    ];

    return $client->search($params);
}

