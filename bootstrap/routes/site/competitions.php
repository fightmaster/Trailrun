<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/competitions/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ListCompetition $listCompetition */
    $listCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ListCompetition::class];

    return $this->view->render($response, '/competitions/list.html.twig',
        ['competitions' => $listCompetition->handle()]
    );
});

$app->get('/competitions/{competitionId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/competitions/edit.html.twig', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);
})->setName('editCompetition');

$app->get('/competitions/{competitionId}/view/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/competitions/view.html.twig', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);

})->setName('viewCompetition');

$app->get('/competitions/create/', function (Request $request, Response $response) {
    return $this->view->render($response, '/competitions/create.html.twig', [
        [],
    ]);
});
