<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/api/competitions/{competitionId}/checkpoint-results/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult $manageCheckpointResult */
    $manageCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\ManageCheckpointResult::class];
    $data = json_decode($request->getBody(), true);
    try {
        $result = $manageCheckpointResult->addByNumber($data);
        if (empty($result)) {
            $response->getBody()->write(
                json_encode(0)
            );
            return;
        }
        $response->getBody()->write(
            json_encode($result)
        );
        return;
    } catch (\InvalidArgumentException $e) {
        return $response->withStatus(400, $e->getMessage());
    }
});

$app->put('/api/competitions/{competitionId}/checkpoint-results/{checkpointResultId}/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\EditCheckpointResult $editCheckpointResult */
    $editCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\EditCheckpointResult::class];
    $data = json_decode($request->getBody(), true);
    try {
        $result = $editCheckpointResult->handle($data);
        if (empty($result)) {
            $response->getBody()->write(
                json_encode(0)
            );
            return;
        }
        $response->getBody()->write(
            json_encode($result)
        );
        return;
    } catch (\InvalidArgumentException $e) {
        return $response->withStatus(400, $e->getMessage());
    }
});

$app->delete('/api/competitions/{competitionId}/checkpoint-results/{checkpointResultId}/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\DeleteCheckpointResult $deleteCheckpointResult */
    $deleteCheckpointResult = $container[\Fightmaster\Trailrun\Competition\Handler\DeleteCheckpointResult::class];
    $deleteCheckpointResult->handle($args['competitionId'], $args['checkpointResultId']);

    return $response->withStatus(204);
})->setName('deleteCheckpointResult');
