<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

return [
    'mysql' => [
        'host'  => '127.0.0.1',
        'database' => 'trailrun',
        'username' => 'user',
        'password' => 'pas'
    ],
    'mongodb' => [
        'host' => 'mongodb://localhost:27017',
        'database' => 'trailrun',
    ],
    'version' => 0.01,
    'slim' => [],
    'log'                => [
        'name'     => 'trailrun-api',
        'handlers' => [
            0 => [
                'type'  => 'StreamHandler',
                'file'  => '/var/log/trailrun/trailrun-api-application.log',
                'level' => 'debug',
            ],
            1 => [
                'type'  => 'StreamHandler',
                'file'  => '/var/log/trailrun/trailrun-api-error.log',
                'level' => 'error',
            ],
        ],
    ],
];
