<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\Database\MongoDB\BaseRepository;
use Fightmaster\Trailrun\StoreItemInterface;

class MemberRepository extends BaseRepository
{
    protected function getCollectionName()
    {
        return 'member';
    }

    /**
     * @param array $searchData
     * @return Member[]
     */
    public function findBy(array $searchData)
    {
        $filter = [];
        if (!empty($searchData['competitionId'])) {
            $filter['competitionId'] = $searchData['competitionId'];
        }
        if (!empty($searchData['tags'])) {
            $filter['tags']['$in'] = $searchData['tags'];
        }
        if (!empty($searchData['number'])) {
            $filter['number'] = $searchData['number'];
        }
        //todo поиск по info


        $cursor = $this->collection->find($filter, ['typeMap' => $this->getTypeMap()]);

        return $this->handleCursorResult($cursor, Member::class);
    }

    public function deleteByCompetitionId($competitionId)
    {
        return $this->deleteCollection(['competitionId' => $competitionId]);
    }

    /**
     * @param string $id
     * @return Member|StoreItemInterface
     */
    public function find($id)
    {
        return $this->_find($id, Member::class);
    }
}
