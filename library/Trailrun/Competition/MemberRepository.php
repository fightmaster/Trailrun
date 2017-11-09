<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\Database\MongoDB\BaseRepository;

class MemberRepository extends BaseRepository
{
    protected function getCollectionName()
    {
        return 'member';
    }

    public function findAll()
    {
        $cursor = $this->collection->find([], ['typeMap' => $this->getTypeMap()]);

        if (empty($cursor)) {
            return [];
        }

        $rows = $cursor->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[] = Member::restore($row);
        }

        return $result;
    }

    public function find($id)
    {
        $row = $this->collection->findOne(['_id' => $id], ['typeMap' => $this->getTypeMap()]);

        if (empty($row)) {
            return null;
        }

        return Member::restore($row);
    }
}
