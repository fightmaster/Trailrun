<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;


use Fightmaster\Trailrun\StoreItemInterface;
use Ramsey\Uuid\Uuid;

class Competition implements \JsonSerializable, StoreItemInterface
{
    private $id;
    private $name;
    private $accessCodes = [];
    protected $info = [];
    protected $metaInfo = [];

    use ManageTagsTrait;

    /**
     * @var Checkpoint[]
     */
    private $checkpoints = [];

    private function __construct()
    {
    }

    public static function create($data)
    {
        $competition = new self();
        $competition->id = Uuid::uuid1()->toString();
        $competition->name = $data['name'];
        $competition->metaInfo['created'] = time();
        $competition->info['startTime'] = $data['startTime'];
        $competition->info['endTime'] = $data['endTime'];

        $competition->alterTags($data['tags']);
        $competition->alterCheckpoints($data['checkpoints']);

        return $competition;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'accessCodes' => $this->accessCodes,
            'tags' => $this->tags,
            'checkpoints' => Checkpoint::toArrayCollection($this->checkpoints),
            'info' => $this->info,
            'metaInfo' => $this->metaInfo,
        ];
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
     * @return Competition
     */
    public static function restore(array $row): Competition
    {
        $row['id'] = $row['_id'];
        unset($row['_id']);

        return self::fromArray($row);
    }

    /**
     * @param array $row
     * @return Competition
     */
    public static function fromArray(array $row): Competition
    {
        $competition = new self();
        $competition->id = $row['id'];
        $competition->name = $row['name'];
        $competition->tags = $row['tags'];
        $competition->accessCodes = $row['accessCodes'];
        $competition->checkpoints = Checkpoint::fromArrayCollection($row['checkpoints']);
        $competition->info = $row['info'];
        $competition->metaInfo = $row['metaInfo'];

        return $competition;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function addAccessCode(AccessCode $accessCode)
    {
        $this->accessCodes[$accessCode->getId()] = $accessCode->toArray();
    }

    public function hasAccess($code, $pass, $access = null)
    {
        if (!isset($this->accessCodes[$code])) {
            return false;
        }

        if ((int)$this->accessCodes[$code]['pass'] !== (int)$pass) {
            return false;
        }

        if (!empty($access)) {
            return $this->accessCodes[$code]['access'] === $access;
        }

        return true;
    }

    public function edit($data)
    {
        $this->name = $data['name'];
        $this->info['startTime'] = $data['startTime'];
        $this->info['endTime'] = $data['endTime'];
        $this->metaInfo['edited'] = time();
    }

    /**
     * @return Checkpoint[]
     */
    public function getCheckpoints()
    {
        return $this->checkpoints;
    }

    public function alterCheckpoints($checkpoints)
    {
        $inserted = [];
        $updated = [];
        $deleted = array_flip(array_keys($this->checkpoints));
        if (!empty($checkpoints)) {
            foreach ($checkpoints as $checkpointData) {
                if (empty($checkpointData['name'])) {
                    continue;
                }
                $checkpointData['sort'] = isset($checkpointData['sort']) ? (int)$checkpointData['sort'] : 0;
                if (empty($checkpointData['id'])) {
                    $checkpoint = Checkpoint::create($this->id, $checkpointData['name'], $checkpointData['sort']);
                    $inserted[$checkpoint->getId()] = $checkpoint;
                } else {
                    $checkpoint = Checkpoint::fromArray($checkpointData);
                    $updated[$checkpoint->getId()] = $checkpoint;
                }
                unset($deleted[$checkpoint->getId()]);
                $this->checkpoints[$checkpoint->getId()] = $checkpoint;
            }
        }
        foreach ($deleted as $key => $item) {
            if (!isset($this->checkpoints[$key])) {
                continue;
            }
            $deleted[$key] = $item;
            unset($this->checkpoints[$key]);
        }

        uasort($this->checkpoints, function ($a, $b)
        {
            return $a->getSort() - $b->getSort();
        });

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'deleted' => $deleted,
        ];
    }

    public function getStartTime()
    {
        return (int)$this->info['startTime'];
    }

    public function getEndTime()
    {
        return (int)$this->info['endTime'];
    }
}
