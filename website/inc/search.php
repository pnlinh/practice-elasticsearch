<?php
/**
 * @critics
 * This is not meant to be beautiful, and it's not how I normally code.
 * I did not decouple functions or classes on purpose, let's chill. I could add
 * more composer packages for DI, or a Micro MVC but I have chosen not to so anyone
 * that looks at this will not have to try very hard!
 *
 * This just displays the use of ElasticSearch (ES) as minimally and simply as possible,
 *   and it's to be framework agnostic so it's any implementation can be done in your
 *   framework of choice.
 *
 * @notes
 * - I refactored the demonstration Source from Laravel to a Framework-less structure.
 * - Originatal source was by "Bo Andersen" in his course: "Complete Guide To Elasticsearch"
 * - I highly recommend this course for ElasticSearch:
 *   https://www.udemy.com/elasticsearch-complete-guide/learn/v4/overview
 *
 * @misc
 * - Function(s) and/or Class(es) are at The Bottom.
 */

/**
 * Require Autoloader
 * @depends elasticsearch/elasticsearch
 */
require 'vendor/autoload.php';

// Yep, PHP 7 Constant array.
define('ES_HOSTS', ['localhost:9200']);

// Get ElasticSearch Instance
$client = getElasticClient(ES_HOSTS);

/**
 * ------------------------------------------------------------------
 * Search Logic
 * ------------------------------------------------------------------
 */
define('RESULTS_PER_PAGE', 5);

// Variables are output on the page and used for:
//  pagination, query display, prices, and other various display items
$variables = [];


$request = new Request();



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

//    $variables['aggregations'] = getSearchFilterAggregations($queryArray);

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
//            'size' => RESULTS_PER_PAGE,
//            'from' => $from,
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

/**
 * ------------------------------------------------------------------
 * Functions
 * ------------------------------------------------------------------
 */
function getSearchFilterAggregations(array $queryArray)
{
    $client = getElasticClient(ES_HOSTS);

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

/**
 * Quick and Sloppy way to get the Client.
 * Need it to use within a function, not writing closures either.
 */
function getElasticClient(array $hosts=[])
{
    return \Elasticsearch\ClientBuilder::create()
        ->setHosts($hosts)
        ->setRetries(2)
        ->build();
}

/**
 * ------------------------------------------------------------------
 * Classes
 * ------------------------------------------------------------------
 */

/**
 * Quick Ad-Hoc way to snag GET values without replacing all the code, lol.
 */
class Request
{
    /**
     * @param $param The name of the $_GET value
     * @return bool|string
     */
    public function query($param) {
      return isset($_GET[$param]) ? trim($_GET[$param]) : false;
    }
}
