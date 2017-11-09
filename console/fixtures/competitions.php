<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

require_once(dirname(__FILE__).'/../init.php');

echo PHP_EOL;

/** @var \MongoDB\Driver\BulkWrite $bulk */
$bulk = $container[\MongoDB\Driver\BulkWrite::class];
/** @var \MongoDB\Driver\Manager $manager */
$manager = $container[\MongoDB\Driver\Manager::class];
$config = $container['settings'];


/** @var \Fightmaster\Trailrun\Competition\AccessCode[] $accessCodes */
$accessCodes = [];
for ($i = 0; $i < 100; $i++) {
    $accessCode = \Fightmaster\Trailrun\Competition\AccessCode::generate();
    $accessCodes[] = $accessCode;
}

foreach ($accessCodes as $accessCode) {
    $bulk->insert($accessCode->toStoreArray());
}

$result = $manager->executeBulkWrite($config['mongodb']['database'] . '.accessCode', $bulk);
echo PHP_EOL;
printf("Inserted %d documents\n", $result->getInsertedCount());
echo PHP_EOL;
