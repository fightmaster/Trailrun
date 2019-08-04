<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/competitions/{competitionId}/members/create/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/members/create.html.twig', [
        'competition' => $viewCompetition->handle($args['competitionId'])
    ]);
})->setName('createMember');
$app->get('/competitions/{competitionId}/members/{memberId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewMember $viewMember */
    $viewMember = $container[\Fightmaster\Trailrun\Competition\Handler\ViewMember::class];

    return $this->view->render($response, '/members/edit.html.twig', $viewMember->handle($args['competitionId'], $args['memberId']));
})->setName('editMember');

$app->get('/competitions/{competitionId}/members/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ListMembers $listMembers */
    $listMembers = $container[\Fightmaster\Trailrun\Competition\Handler\ListMembers::class];

    return $this->view->render($response, '/members/list.html.twig', $listMembers->handle($args['competitionId']));
})->setName('competitionsMembers');
