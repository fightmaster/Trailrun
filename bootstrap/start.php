<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'development'));

require_once dirname(__DIR__) . '/vendor/autoload.php';
if (APPLICATION_ENV == 'testing') {
    $config = require __DIR__.'/../config/config.test.php';
} else {
    $config = require __DIR__.'/../config/config.php';
}

$app = new \Slim\App(['settings' => $config]);
require 'locator.php';
require 'routes.php';
