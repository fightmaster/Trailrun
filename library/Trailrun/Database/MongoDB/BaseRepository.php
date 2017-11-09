<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Database\MongoDB;

use Fightmaster\Trailrun\StoreItemInterface;
use MongoDB\Collection;
use MongoDB\Database;

abstract class BaseRepository
{
    /**
     * @var Database;
     */
    protected $database;

    /**
     * @var Collection
     */
    protected $collection;

    abstract protected function getCollectionName();

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = $this->database->{$this->getCollectionName()};
    }

    public function insert(StoreItemInterface $object)
    {
        $this->collection->insertOne($object->toStoreArray());
    }

    public function update(StoreItemInterface $object)
    {
        $this->collection->updateOne(['_id' => $object->getId()], ['$set' => $object->toStoreArray()]);
    }

    public function exist($id)
    {
        return !empty($this->collection->findOne(['_id' => $id]));
    }

    protected function getTypeMap()
    {
        return ['root' => 'array', 'document' => 'array', 'array' => 'array'];
    }
}
