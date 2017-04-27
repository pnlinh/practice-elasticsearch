<?php

/**
 * Quick Ad-Hoc way to snag GET values without replacing all the code, lol.
 *
 * I just need a default value option, and less code to retype, so here it is in it's glory.
 */
class Request
{
    /**
     * @param $param The name of the $_GET value
     * @param $default Default value to provide
     *
     * @return bool|string
     */
    public function query($param, $default = null) {
      return isset($_GET[$param]) ? trim($_GET[$param]) : $default;
    }
}
