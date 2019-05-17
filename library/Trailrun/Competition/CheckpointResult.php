<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\StoreItemInterface;
use Ramsey\Uuid\Uuid;

class CheckpointResult implements StoreItemInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $competitionId;

    /**
     * @var string
     */
    private $checkpointId;

    /**
     * @var string
     */
    private $memberId;

    /**
     * @var int
     */
    private $time;

    /**
     * @var array
     */
    private $metaInfo;

    private function __construct()
    {
    }

    /**
     * @param string $competitionId
     * @param string|null $memberId
     * @param int|null $time
     * @param null $checkpointId
     * @return CheckpointResult
     */
    public static function create($competitionId, $memberId = null, $time = null, $checkpointId = null): CheckpointResult
    {
        $checkpointResult = new self();
        $timeForId = $time ?? time() + rand(-1000, 1000);
        $checkpointResult->id = Uuid::uuid5(Uuid::NAMESPACE_X500, $competitionId . '_' . $memberId . '_' . $timeForId . '_' . $checkpointId)->toString();
        $checkpointResult->competitionId = $competitionId;
        $checkpointResult->checkpointId = $checkpointId;
        $checkpointResult->memberId = $memberId;
        $checkpointResult->time = $time;
        $checkpointResult->metaInfo['created'] = time();

        return $checkpointResult;
    }

    public function edit($data)
    {

    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCompetitionId()
    {
        return $this->competitionId;
    }

    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @return string
     */
    public function getCheckpointId()
    {
        return $this->checkpointId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'competitionId' => $this->competitionId,
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
        $checkpointResult->competitionId = $row['competitionId'];
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
