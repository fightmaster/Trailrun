<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

require_once(dirname(__FILE__).'/../init.php');

use \Fightmaster\Trailrun\User\User;
use \Ramsey\Uuid\Uuid;

echo PHP_EOL;

/** @var \MongoDB\Driver\BulkWrite $bulk */
$bulk = $container[\MongoDB\Driver\BulkWrite::class];
/** @var \MongoDB\Driver\Manager $manager */
$manager = $container[\MongoDB\Driver\Manager::class];
$config = $container['settings'];


/** @var \Fightmaster\Trailrun\User\User[] $users */
$uuid = Uuid::uuid1()->toString();
$row = 0;
$keys = [];
$users = [];
if (($handle = fopen(dirname(__FILE__).'/../../data/fixtures/users.csv', "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $num = count($data);
        if ($row == 0) {
            for ($i = 0; $i < $num; $i++) {
                $keys[$i] = $data[$i];
            }
            $row++;
            continue;
        }
        $userData = [];
        for ($i = 0; $i < $num; $i++) {
            $userData[$keys[$i]] = $data[$i];
        }
        if (!empty($userData['_id'])) {
            $user = User::restore($userData);
        } else {
            $user = User::create($userData);
        }
        $users[] = $user;
        $row++;
    }
    fclose($handle);
}

if (empty($users)) {
    die('No data for inserts');
}
foreach ($users as $user) {
    $bulk->insert($user->toStoreArray());
}

$result = $manager->executeBulkWrite($config['mongodb']['database'] . '.users', $bulk);
echo PHP_EOL;
printf("Inserted %d documents\n", $result->getInsertedCount());
echo PHP_EOL;
