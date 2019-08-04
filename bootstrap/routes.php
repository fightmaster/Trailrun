<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */
use Slim\Http\Request;
use Slim\Http\Response;

require 'routes/api/competitions.php';
require 'routes/api/members.php';
require 'routes/api/checkpoint-results.php';
require 'routes/site/competitions.php';
require 'routes/site/members.php';
require 'routes/site/results.php';

$app->get('/', function (Request $request, Response $response, $args) use ($container) {

    return $this->view->render($response, '/index/about.html.twig', []);
})->setName('index');
