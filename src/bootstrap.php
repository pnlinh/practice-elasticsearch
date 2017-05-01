<?php

/**
 * --------------------------------------------------------------------------
 * Autoload the Elastic Search API
 * --------------------------------------------------------------------------
 */
require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

/**
 * --------------------------------------------------------------------------
 * Require API Class with all search Queries
 * Note: I didn't want to load in composer so it's easier to follow here.
 * --------------------------------------------------------------------------
 */
require 'Api/Request.php';
require 'Api/Api.php';

/**
 * --------------------------------------------------------------------------
 * Use $api as our connector, run any command from there.
 * --------------------------------------------------------------------------
 */
$api = new Api\Api(['localhost:9200']);
