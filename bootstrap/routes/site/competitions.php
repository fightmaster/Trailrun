<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Fightmaster\Trailrun\Competition\Handler\ListCompetition;
use Fightmaster\Trailrun\Competition\Handler\ViewCompetition;

$app->get('/competitions/', function (Request $request, Response $response, $args) use ($container) {
    /** @var ListCompetition $listCompetition */
    $listCompetition = $container[ListCompetition::class];

    return $this->view->render($response, '/competitions/list.html.twig',
        ['competitions' => $listCompetition->handle()]
    );
});

$app->get('/competitions/{competitionId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var ViewCompetition $viewCompetition */
    $viewCompetition = $container[ViewCompetition::class];

    return $this->view->render($response, '/competitions/edit.html.twig', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);
})->setName('editCompetition');

$app->get('/competitions/{competitionId}/view/', function (Request $request, Response $response, $args) use ($container) {
    /** @var ViewCompetition $viewCompetition */
    $viewCompetition = $container[ViewCompetition::class];

    return $this->view->render($response, '/competitions/view.html.twig', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);

})->setName('viewCompetition');

$app->get('/competitions/create/', function (Request $request, Response $response) {
    return $this->view->render($response, '/competitions/create.html.twig', [
        [],
    ]);
});
