<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\StoreItemInterface;
use Ramsey\Uuid\Uuid;

class CheckpointResult implements StoreItemInterface
{
    private $id;
    private $checkpointId;
    private $memberId;
    private $time;
    private $metaInfo;

    private function __construct()
    {
    }

    public static function create($memberId, $time = null, $checkpointId = null)
    {
        $checkpointResult = new self();
        $timeForId = $time ?? time() + rand(-1000, 1000);
        $checkpointResult->id = Uuid::uuid5(Uuid::NAMESPACE_X500, $memberId . '_' . $timeForId . '_' . $checkpointId);
        $checkpointResult->checkpointId = $checkpointId;
        $checkpointResult->memberId = $memberId;
        $checkpointResult->time = $time;
        $checkpointResult->metaInfo['created'] = time();
    }

    public function edit($data)
    {

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'checkpointId' => $this->checkpointId,
            'memberId' => $this->memberId,
            'time' => $this->time,
            'metaInfo' => $this->metaInfo,
        ];
    }

    /**
     * @param array $row
     * @return CheckpointResult
     */
    public static function fromArray(array $row): CheckpointResult
    {
        $checkpointResult = new self();
        $checkpointResult->id = $row['id'];
        $checkpointResult->checkpointId = $row['checkpointId'];
        $checkpointResult->memberId = $row['memberId'];
        $checkpointResult->time = $row['time'];
        $checkpointResult->metaInfo = $row['metaInfo'];

        return $checkpointResult;
    }

    /**
     * @return array
     */
    public function toStoreArray(): array
    {
        $toStore = $this->toArray();
        $toStore['_id'] = $toStore['id'];
        unset($toStore['id']);

        return $toStore;
    }

    /**
     * @param array $row
     * @return CheckpointResult
     */
    public static function restore(array $row): CheckpointResult
    {
        $row['id'] = $row['_id'];
        unset($row['_id']);

        return self::fromArray($row);
    }

}
