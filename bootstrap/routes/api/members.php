<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

use Slim\Http\Request;
use Slim\Http\Response;

$app->post('/api/competitions/{competitionId}/members/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\CreateMember $createMember */
    $createMember = $container[\Fightmaster\Trailrun\Competition\Handler\CreateMember::class];

    $data = json_decode($request->getBody(), true);
    try {
        $created = $createMember->handle($data);
        $response->getBody()->write(
            json_encode($created->toArray())
        );
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }
});

$app->delete('/api/competitions/{competitionId}/members/{memberId}/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\DeleteMember $deleteMember */
    $deleteMember = $container[\Fightmaster\Trailrun\Competition\Handler\DeleteMember::class];
    $deleteMember->handle($args['competitionId'], $args['memberId']);

    return $response->withStatus(204);
})->setName('deleteMember');

$app->put('/api/competitions/{competitionId}/members/{memberId}/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\EditMember $editMember */
    $editMember = $container[\Fightmaster\Trailrun\Competition\Handler\EditMember::class];
    $data = json_decode($request->getBody(), true);
    try {
        $edited = $editMember->handle($data);
        $response->getBody()->write(
            json_encode($edited->toArray())
        );
        return;
    } catch (\InvalidArgumentException $e) {
        $response->withStatus(400, $e->getMessage());
        return;
    }

});
