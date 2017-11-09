<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests;


use Interop\Container\ContainerInterface;
use MongoDB\Database;

class TrailrunTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
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
}
