<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/api/tickets', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new \Fightmaster\Trailrun\Competition\CompetitionRepository($this->db);
    $tickets = $mapper->findAll();

    $data = [
        'aab5d5fd-70c1-11e5-a4fb-b026b977eb28',
        $bin = (binary)'aab5d5fd-70c1-11e5-a4fb-b026b977eb28',
        $char = (string)$bin,
        $char = (int)$bin,
    ];
    $response->getBody()->write(
        json_encode($data)
    );
    return $response;
});


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


$app->get('/competitions/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ListCompetition $listCompetition */
    $listCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ListCompetition::class];

    return $this->view->render($response, '/competitions/list.html',
        ['competitions' => $listCompetition->handle()]
    );
});

$app->get('/competitions/{competitionId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/competitions/edit.html', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);
})->setName('editCompetition');

$app->get('/competitions/{competitionId}/view/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/competitions/view.html', [
        'competition' => $viewCompetition->handle($args['competitionId']),
    ]);

})->setName('viewCompetition');
//
//$app->get('/competitions/{competitionId}/members/', function (Request $request, Response $response, $args) use ($container) {
//    /** @var \Fightmaster\Trailrun\Competition\Handler\ListMembers $listMembers */
//    $listMembers = $container[\Fightmaster\Trailrun\Competition\Handler\ListMembers::class];
//
//    return $this->view->render($response, '/competitions/members.html', [
//        $listMembers->handle($args['competitionId']),
//    ]);
//
//})->setName('viewMembers');


$app->get('/competitions/create/', function (Request $request, Response $response) {
    return $this->view->render($response, '/competitions/create.html', [
        [],
    ]);
});
$app->get('/competitions/{competitionId}/members/create/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewCompetition $viewCompetition */
    $viewCompetition = $container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class];

    return $this->view->render($response, '/members/create.html', [
        'competition' => $viewCompetition->handle($args['competitionId'])
    ]);
})->setName('createMember');
$app->get('/competitions/{competitionId}/members/{memberId}/edit/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ViewMember $viewMember */
    $viewMember = $container[\Fightmaster\Trailrun\Competition\Handler\ViewMember::class];

    return $this->view->render($response, '/members/edit.html', $viewMember->handle($args['competitionId'], $args['memberId']));
})->setName('editMember');

$app->get('/competitions/{competitionId}/members/', function (Request $request, Response $response, $args) use ($container) {
    /** @var \Fightmaster\Trailrun\Competition\Handler\ListMembers $listMembers */
    $listMembers = $container[\Fightmaster\Trailrun\Competition\Handler\ListMembers::class];

    return $this->view->render($response, '/members/list.html', $listMembers->handle($args['competitionId']));
});
