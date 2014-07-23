<?php

$GLOBALS['config'] = array(
    'mysql' => array(
        'use'       => true,
        'host'      => '',
        'username'  => '',
        'password'  => '',
        'db'        => ''
    ),
);

class Config {

    public static function get($path = null) {
        if (!($path)) {
            return null;
        }

        $config = $GLOBALS['config'];
        $path = explode('/', $path);

        return $config[$path[0]][$path[1]];
    }

}

?>
