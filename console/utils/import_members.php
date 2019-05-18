<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

require_once(dirname(__FILE__).'/../init.php');

use \Fightmaster\Trailrun\Competition\Handler\ImportMembers;

echo PHP_EOL;

$sourceFile = __DIR__ . '/ВКМ 2019.csv';
$competitionId = '00f1920a-7958-11e9-9af4-80e6500c557e';
$maps = [
    'number' => 0,
    'firstName' => 4,
    'lastName' => 3,
    'dob' => 5,
    'clubName' => 8,
    'city' => 7,
    'gender' => 6,
    'phone' => 9,
    'email' => 10,
    'tags' => [1, 2],
    'registrationDate' => 12
];
/** @var ImportMembers $import */
$import = $container[ImportMembers::class];
$data = [];
$data['competitionId'] = $competitionId;
$data['filepath'] = $sourceFile;
$data['maps'] = $maps;

$result = $import->handle($data);
printf(PHP_EOL . 'Rows %s were inserted.' . PHP_EOL, $result->getInsertedCount());
