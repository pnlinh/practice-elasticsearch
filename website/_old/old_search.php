<?php
/**
 * This is a simple implementation.
 * In a production environment you would sanitize and validate user data,
 * but for learning this, simplicity is best.
 */
require 'vendor/autoload.php';


$elastic = Elasticsearch\ClientBuilder::create()
->setHosts(['localhost:9200'])
    ->setRetries(2)
    ->build();

/**
 * Attempt to get the post query from field.
 *
 * > $_POST comes through assets/old_app.js
 * > With the form on index.html #search-form
 *      The search field name="query"
 *      The action="old_search.php" is important, assets/old_app.js reads the path there to post to.
 *
 */
$query = isset($_POST['query']) ? trim($_POST['query']) : false;

// Pagination
$per_page = 3;
$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
if ($page <= 0) {
    $page = 1;
}

// Indexes are 0 based, so 0 x 5 = 0, 1 = 5 = 5, 10, 15 20, etc.
$from = ($page - 1) * $per_page;
// end Pagination

if (empty($query)) {
    return $output([], $query, "<b>Oops!</b> No Search Query was Provided, please retry filling in the ");
}

$variables['aggregations'] = getfilterAgg();

// Dynamically Add Fuzzy Queries for spelling errors
$query = [
    'bool' => [
        'must' => []
    ],
];

// Generates Terms dynamically (separated by a space),
// They are an array of terms to iterate through
$terms = explode(' ', $query);

foreach ($terms as $token) {
    $query['bool']['must'][] = [
        'match' => [
            'name' => [
                'query' => $token,
                'fuzziness' => '3', // Up to 3 letters
            ]
        ],
    ];
}

$params = [
    'index' => 'ecommerce',
    'type' => 'product',
    'body' => [
        'query' => $query,
        // Pagination
        'size' => $per_page,
        'from' => $from
    ],
];

function getfilterAgg() {
    $params = [
        'index' => 'ecommerce',
        'type' => 'product',
        'body' => [
            'query' => [
                'match_all' => new \stdClass()
            ],
            'size' => 0,
            'aggs' => [
                'status' => [
                    'terms' => [ 'field' => 'status' ],

                ],
                'price_range' => [
                    'range' => [ 'field' => 'price' ],
                    'ranges' => [
                        [ 'from' => 1, 'to' => 25 ],
                        [ 'from' => 25, 'to' => 50 ],
                        [ 'from' => 50, 'to' => 75 ],
                        [ 'from' => 75, 'to' => 100 ]
                    ],
                ],

                // Categories are Nested special type
                'categories' => [
                    'nested' => [
                        // Path is name of field containing objects
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

    // todo: need to fix
    return $elastic->search($params);
}



/**
 * Attempt to get the search results
 */
$result = $elastic->search($params);

/**
 * Get the total results
 */
$total = $result['hits']['total'];

/**
 * Return an Error when empty
 */

if ($total <= 0) {
    Output::json([], $query, 'No Records were Found for "<b>'. $query .'</b>", please try again in the ');
    exit;
}

/**
 * Add custom values that will be needed,
 * I will nest it under the "extra" array.
 */
$to = ($page * $per_page);
$to = ($to > $total ? $total : $to);

Output::setExtra([
    'total' => $result,
    'page' => $page,
    'from' => $total,
    'to' =>  $to,
]);

Output::json($result['hits']['hits'], $query);
exit;

/**
 * Output to JSON via echo so I can use the return statement to end the script.
 *
 * @param mixed  $data
 * @param string $query  Would like to know the query passed.
 * @param mixed  $error
 * @return void
 */

class Output {

    protected static $extra = [];

    public static function setExtra($extra = []) {
        self::$extra = (array) $extra;
    }

    public static function json($data, $query, $error = false) {
        header('Content-Type: application/json');
        echo json_encode([
            'data' => $data,
            'query' => $query,
            'extra' => self::$extra,
            'error' => ($error) ?? false
        ]);
    }
}
