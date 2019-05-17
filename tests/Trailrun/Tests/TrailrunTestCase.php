<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests;


use Interop\Container\ContainerInterface;
use MongoDB\Database;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TrailrunTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp(): void
    {
        require __DIR__ . '/../../../bootstrap/start.php';

        /** @var ContainerInterface $container */
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }

    protected function truncateMongoDbCollections($collectionNames)
    {
        /** @var Database $db */
        $db = $this->getContainer()->get('db');
        foreach ($collectionNames as $collectionName) {
            $db->dropCollection($collectionName);
        }
    }

    protected function generateUuid1()
    {
        return Uuid::uuid1()->toString();
    }
}
