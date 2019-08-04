<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */
use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/api/competitions/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\CreateCompetition $createCompetition */
    $createCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\CreateCompetition::class];

    $data = json_decode($request->getBody(), true);
    try {
        $created = $createCompetition->handle($data);
        $response->getBody()->write(
            json_encode($created->toArray())
        );
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }
});
$app->put('/api/competitions/{competitionId}/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\EditCompetition $editCompetition */
    $editCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\EditCompetition::class];

    $data = json_decode($request->getBody(), true);
    try {
        $created = $editCompetition->handle($data);
        $response->getBody()->write(
            json_encode($created->toArray())
        );
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }
});

$app->post('/api/competitions/{competitionId}/start-1/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult $manageCheckpointResult */
    $manageCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult::class];
    try {
        $manageCheckpointResult->startOne($args['competitionId']);
        $response->withStatus(200);
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }
})->setName('start-1');

$app->post('/api/competitions/{competitionId}/start-2/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult $manageCheckpointResult */
    $manageCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult::class];
    try {
        $manageCheckpointResult->startTwo($args['competitionId']);
        $response->withStatus(200);
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }
})->setName('start-2');
