<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;


$app->get('/competitions/{competitionId}/last-results/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\CheckpointResults $checkpointResults */
    $checkpointResults = $container[\Fightmaster\Trailrun\Competition\Handler\CheckpointResults::class];
    $lastResults = $checkpointResults->last($args['competitionId'], 20);

    return $this->view->render($response, '/results/last_results.html.twig', $lastResults);
})->setName('competitionLastResults');

$app->get('/competitions/{competitionId}/results/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetitionResults $viewCompetitionResults */
    $viewCompetitionResults = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetitionResults::class];

    return $this->view->render($response, '/results/results.html.twig', $viewCompetitionResults->handle($args['competitionId']));
})->setName('competitionResults');

$app->get('/competitions/{competitionId}/manage-results/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    /** @var \Fightmaster\Trailrun\Competition\Handler\CheckpointResults $checkpointResults */
    $checkpointResults = $container[\Fightmaster\Trailrun\Competition\Handler\CheckpointResults::class];
    $lastResults = $checkpointResults->last($args['competitionId'], 20);
    $allResults = $checkpointResults->all($args['competitionId']);

    return $this->view->render($response, '/results/manage.html.twig', [
        'lastResults' => $lastResults,
        'competition' => $viewCompetition->handle($args['competitionId']),
        'allResults' => $allResults
    ]);
})->setName('competitionManageResults');

$app->get('/competitions/{competitionId}/checkpoint-results/{checkpointResultId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCheckpointResult $viewCheckpointResult */
    $viewCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCheckpointResult::class];

    return $this->view->render($response, '/results/edit_checkpoint_result.html.twig', $viewCheckpointResult->handle($args['competitionId'], $args['checkpointResultId']));
})->setName('editCheckpointResult');
