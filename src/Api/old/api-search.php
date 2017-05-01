<?php

/**
 * Required Files
 * @desc Purposely included this; So it's obvious what I'm doing.
 */

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/request.php';  // A $request->query() wrapper for $_GET
require dirname(__DIR__) . '/src/client.php';   // The Elastic Client



/**
 * ------------------------------------------------------------------
 * Search Logic
 * ------------------------------------------------------------------
 */
define('RESULTS_PER_PAGE', 5);

// Variables are output on the page and used for:
// such as: pagination, query display, prices, and other various display items
$variables = [];


// This is just a rigged $_GET method :) @ the bottom of file.
$request = new Request();


// We try to run an Elastic Query if ?query=<string> exists in the URI
if ($query = $request->query('query')) {

    // ** Only Get Elastic if a query is loaded up **
    // Removed PHP7 constant incase someones on php5.
    // (Used in two places in this file)
    $es_hosts = ['localhost:9200'];

    // Get ElasticSearch Instance
    $client = getElasticClient($es_hosts);

    // Get our Index/Type (Database/Table)
    $es_index = 'inventory';
    $es_type = 'product';

    $index_and_type = [
        'index' => $es_index,
        'type' => $es_type
    ];

//    $es_type = $request->query('subcategory', 'manufacturer');
//    if ( ! in_array($es_type, ['manufacturer', 'featured', 'firearms'])) {
//        throw new \InvalidArgumentException('Must be one of: manufacturer, featured, firearms');
//    }


    // Get our Query (the <form> posts ?query=xyz&page=<0-9>
    $query = trim($query);
    $page = $request->query('page', 1);
    $from = (($page - 1) * RESULTS_PER_PAGE);   // start/from is the same as LIMIT 0,10 in SQL

    // Don't let from be negative
    $from = ($from < 0) ? 0 : $from;


    // Variables are for view display
    $variables['page'] = $page;
    $variables['from'] = $from;
    $variables['query'] = $query;

    // This dynamically creates an array of search terms to match.
    // We need a template to rebuild upon for many options, it starts here:
    $queryArray = [
        'bool' => [
            'must' => [],
            'filter' => [],
        ],
    ];

    // (+) Our query might contain: "the dog jumps",
    //     We turn this into: ['the', 'dog', 'jumps']
    $tokens = explode(' ', $query);

    // We now create a set of rules to match for each word above.
    // (+) If there were three terms (token), this adds three blocks of ['match' => ...]
    //
    // (+) fuzziness allows there to be a spelling error,
    //      'AUTO' defaults to 1
    //      You can use an integer, such as for 3 for 3 characters.
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

    // (+) Aggregations puts our items into "Buckets", more specifically an array of many buckets.
    //      such as: [bucket => ...the data we want here... ]
    //
    // (!) These are like building blocks to make queries more and more complex. You can keep nesting
    //     aggs (short name) to get very granualry
    //
    // (+) This applies all the rules above that we had.
    $variables['aggregations'] = getSearchFilterAggregations($queryArray, $es_hosts, $index_and_type);

    // Filter
    $startPrice = $request->query('startprice');
    $endPrice   = $request->query('endprice');
    $status     = $request->query('status');
    $category   = $request->query('category');


    // Variables are for view display
    $variables['startPrice'] = $startPrice;
    $variables['endPrice'] = $endPrice;
    $variables['status'] = $status;
    $variables['category'] = $category;
    $variables['subcategory'] = $es_type;

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
//    if ($category) {
//        // @TODO multi category narrowing down
//        // This comes from a URI so I can use a CSV to do this.
//        $category_list = explode(',', $category);
//
//        foreach ($category_list as $category) {
//            $queryArray['bool']['filter'][] = [
//                'nested' => [
//                    'path' => 'categories',
//                    'query' => [
//                        'term' => [
//                            'categories.name.keyword' => $category,
//                        ],
//                    ],
//                ],
//            ];
//        }
//    }

    $params = [
        'index' => $es_index,
        'type' => $es_type,
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

    $page_count = ceil($total / RESULTS_PER_PAGE);

    $variables['to'] = $to;


    // start:QueryStringBuilder
    /**
     * Use to make query string building simpler.
     * @param array|bool $replace Array (One or Many)
     * @return string Returns QueryString with Array replacement
     */
    $query_array = [
        'query' => $query,
        'page' => $page,
        'status' => $status,
        'startprice' => $startPrice,
        'endprice' => $endPrice,
        'category' => $category,
    ];

    // Pass an array for replacing
    $variables['query_string'] = function($replace_or_add = false) use ($query_array) {
        if ($replace_or_add) {
            // We loop as there may be many!
            foreach($replace_or_add as $key => $value) {
                // This isnt right lol, this is so hackish :D
//                if ( array_key_exists('category', $query_array )) {
//                    if ($query_array['category'] !== $value) {
//                        $query_array['category'] += ",$value";
//                        ChromePhp::log($query_array['category']);
//                    }
//                }
                // This will replace, or add
                $query_array[$key] = $value;
            }
        }

        // remove empty values.
        $query_array = array_filter($query_array);


        return "?" . http_build_query($query_array);
    };
    // end:QueryStringBuilder


    if (isset($result['hits']['hits'])) {
        $variables['hits'] = $result['hits']['hits'];
    }

    // So I can use the actual variable name, varvar, lol.
    foreach ($variables as $key => $value) {
        $$key = $value;
    }
}

/**
 * ------------------------------------------------------------------
 * Functions
 * ------------------------------------------------------------------
 */
function getSearchFilterAggregations(array $queryArray, $es_hosts = false, array $index_and_type = [])
{
    $client = getElasticClient($es_hosts);

    $params = [
        'index' => $index_and_type['index'],
        'type' => $index_and_type['type'],
        'body' => [
            'query' => $queryArray,
            //'size' => RESULTS_PER_PAGE,  // 0 will be NO RESULTS, defaults at 10
            'aggs' => [
                'statuses' => [
                    // Mapped as a keyword, don't use .keyword
                    'terms' => [ 'field' => 'status' ]
                ],

                // Aggregated Price Range (per Bucket applied to each Query)
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

                // Aggregated Categories (per Bucket applied to each Query)
                'categories' => [
                    'nested' => [
                        'path' => 'categories',
                    ],
                    'aggs' => [
                        'categories_count' => [
                            'terms' => [ 'field' => 'categories.name.keyword' ]
                        ],
                    ],
                ],
            ],
        ],
    ];

    ChromePhp::log(json_encode($params));

    return $client->search($params);
}

//function globalCategoryFilterAggs($data = false, $es_hosts) {
//
//    $client = getElasticClient($es_hosts);
//
//    $params = [
//    "aggs" : {
//        "t_shirts" : {
//            "filter" : { "term": { "type": "t-shirt" } },
//            "aggs" : {
//                "avg_price" : { "avg" : { "field" : "price" } }
//            }
//        }
//    }
//    return $client->search($params);
//}
