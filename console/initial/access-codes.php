<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

require_once(dirname(__FILE__).'/../init.php');

echo PHP_EOL;

/** @var \Fightmaster\Trailrun\Competition\AccessCodeRepository $accessCodeRepository */
$accessCodeRepository = $container[\Fightmaster\Trailrun\Competition\AccessCodeRepository::class];
/** @var \MongoDB\Driver\BulkWrite $bulk */
$bulk = $container[\MongoDB\Driver\BulkWrite::class];
/** @var \MongoDB\Driver\Manager $manager */
$manager = $container[\MongoDB\Driver\Manager::class];
$config = $container['settings'];


/** @var \Fightmaster\Trailrun\Competition\AccessCode[] $accessCodes */
$accessCodes = [];
$j = 0;
for ($i = 0; $i < 100; $i++) {
    $accessCode = \Fightmaster\Trailrun\Competition\AccessCode::generate();
    $exist = $accessCodeRepository->exist($accessCode->getId());
    if ($exist) {
        continue;
    }
    $accessCodes[] = $accessCode;
    $accessCodeRepository->insert($accessCode);
    $j++;
}

echo PHP_EOL;
printf("Inserted %d documents\n", $j);
echo PHP_EOL;
