<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Database\MongoDB;

use Fightmaster\Trailrun\StoreItemInterface;
use MongoDB\Collection;
use MongoDB\Database;
use MongoDB\Driver\Cursor;
use MongoDB\InsertManyResult;

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

    /**
     * @param StoreItemInterface $object
     * @return \MongoDB\InsertOneResult
     */
    public function insert(StoreItemInterface $object)
    {
        return $this->collection->insertOne($object->toStoreArray());
    }

    /**
     * @param StoreItemInterface[] $collection
     * @return InsertManyResult
     */
    public function insertCollection($collection)
    {
        $objects = [];
        foreach ($collection as $item) {
            if (!$item instanceof StoreItemInterface) {
                throw new \LogicException('Every item of collection should be implement StoreItemInterface');
            }
            $objects[] = $item->toStoreArray();
        }

        return $this->collection->insertMany($objects);
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

    /**
     * @param Cursor $cursor
     * @param string $storeItemClass
     * @return array
     */
    protected function handleCursorResult(Cursor $cursor, string $storeItemClass)
    {
        if (empty($cursor)) {
            return [];
        }

        $rows = $cursor->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[] = $storeItemClass::restore($row);
        }

        return $result;
    }

    /**
     * @param string $id
     * @param string $findItemClass
     * @return StoreItemInterface
     */
    protected function _find(string $id, string $findItemClass)
    {
        $row = $this->collection->findOne(['_id' => $id], ['typeMap' => $this->getTypeMap()]);

        if (empty($row)) {
            return null;
        }

        return $findItemClass::restore($row);
    }
}
