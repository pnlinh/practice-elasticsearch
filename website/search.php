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
 * > $_POST comes through assets/app.js
 * > With the form on index.html #search-form
 *      The search field name="query"
 *      The action="search.php" is important, assets/app.js reads the path there to post to.
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

$params = [
    'index' => 'ecommerce',
    'type' => 'product',
    'body' => [
        'query' => [
            'match' => [
                'name' => $query
            ],
        ],
        // Pagination
        'size' => $per_page,
        'from' => $from
    ],
];

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
