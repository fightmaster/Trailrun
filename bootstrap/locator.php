<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */
$container = $app->getContainer();

$container['db'] = function ($container) {
    $config = $container['settings'];
    $database = $config['mongodb'];
    $client = new \MongoDB\Client($database['host']);
    return $client->selectDatabase($database['database']);
};

$container['logger'] = function ($container) {
    $logger = new \Monolog\Logger('logger');
    $config = $container['settings'];
    $fileHandler = new \Monolog\Handler\StreamHandler($config['log']['handlers'][0]['file']);
    $logger->pushHandler($fileHandler);
    return $logger;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../templates', [
        'cache' => '/var/www/cache/',
        'auto_reload' => true,
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

//repositories
$container[\Fightmaster\Trailrun\Competition\AccessCodeRepository::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\AccessCodeRepository($container['db']);
};
$container[\Fightmaster\Trailrun\Competition\CompetitionRepository::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\CompetitionRepository($container['db']);
};
$container[\Fightmaster\Trailrun\Competition\MemberRepository::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\MemberRepository($container['db']);
};


//handlers

//competitions
$container[\Fightmaster\Trailrun\Competition\Handler\CreateCompetition::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\Handler\CreateCompetition(
        $container[\Fightmaster\Trailrun\Competition\CompetitionRepository::class],
        $container[\Fightmaster\Trailrun\Competition\AccessCodeRepository::class]
    );
};
$container[\Fightmaster\Trailrun\Competition\Handler\EditCompetition::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\Handler\EditCompetition(
        $container[\Fightmaster\Trailrun\Competition\CompetitionRepository::class]
    );
};
$container[\Fightmaster\Trailrun\Competition\Handler\ListCompetition::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\Handler\ListCompetition(
        $container[\Fightmaster\Trailrun\Competition\CompetitionRepository::class]
    );
};
$container[\Fightmaster\Trailrun\Competition\Handler\ViewCompetition::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\Handler\ViewCompetition(
        $container[\Fightmaster\Trailrun\Competition\CompetitionRepository::class]
    );
};

//members
$container[\Fightmaster\Trailrun\Competition\Handler\CreateMember::class] = function ($container) {
    return new \Fightmaster\Trailrun\Competition\Handler\CreateMember(
        $container[\Fightmaster\Trailrun\Competition\MemberRepository::class]
    );
};

$container[\MongoDB\Driver\BulkWrite::class] = function ($container) {
    return new MongoDB\Driver\BulkWrite();
};
$container[\MongoDB\Driver\Manager::class] = function ($container) {
    $config = $container['settings'];
    $database = $config['mongodb'];
    return new MongoDB\Driver\Manager($database['host']);
};


