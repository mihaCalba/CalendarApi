<?php

require_once 'Env.php';

class Config {

    public static function get()
    {
        return [
            'displayErrorDetails'               => true,
            'addContentLengthHeader'            => false,
            'determineRouteBeforeAppMiddleware' => true,

            'db' => [
                'driver'    => 'mysql',
                'host'      => Env::get('DB_HOST'),
                'username'  => Env::get('DB_USER'),
                'password'  => Env::get('DB_PASS'),
                'database'  => Env::get('DB_NAME'),
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix'    => '',
            ]
        ];
    }
}
