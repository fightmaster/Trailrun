<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\Database\MongoDB\BaseRepository;
use Fightmaster\Trailrun\StoreItemInterface;

class CheckpointResultRepository extends BaseRepository
{
    protected function getCollectionName()
    {
        return 'checkpointResult';
    }

    /**
     * @param $id
     * @return CheckpointResult|StoreItemInterface
     */
    public function find($id)
    {
        return $this->_find($id, CheckpointResult::class);
    }

    /**
     * @param array $searchData
     * @return CheckpointResult[]
     */
    public function findBy(array $searchData)
    {
        $filter = [];
        if (!empty($searchData['competitionId'])) {
            $filter['competitionId'] = $searchData['competitionId'];
        }
        if (!empty($searchData['checkpointIds'])) {
            $filter['checkpointId']['$in'] = $searchData['checkpointIds'];
        }
        if (!empty($searchData['memberIds'])) {
            $filter['memberId']['$in'] = $searchData['memberIds'];
        }
        if (!empty($searchData['memberId'])) {
            $filter['memberId'] = $searchData['memberId'];
        }
        $cursor = $this->collection->find($filter, ['typeMap' => $this->getTypeMap()]);

        return $this->handleCursorResult($cursor, CheckpointResult::class);
    }
}
