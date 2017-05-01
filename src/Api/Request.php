<?php

namespace Api;

/**
 * Class Request
 *
 * @package Api
 */
class Request
{
    /**
     * @param $param The name of the $_GET value
     * @param $default Default value to provide
     * @return bool|string
     */
    public static function query($param, $default = null) {
        return isset($_GET[$param]) ? trim($_GET[$param]) : $default;
    }
}
