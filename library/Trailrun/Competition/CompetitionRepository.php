<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\Database\MongoDB\BaseRepository;
use Fightmaster\Trailrun\StoreItemInterface;

class CompetitionRepository extends BaseRepository
{
    protected function getCollectionName()
    {
        return 'competition';
    }

    public function findAll()
    {
        $cursor = $this->collection->find([], ['typeMap' => $this->getTypeMap(), 'sort' => ['info.startTime' => -1]]);

        return $this->handleCursorResult($cursor, Competition::class);
    }

    /**
     * @param string $id
     * @return Competition|StoreItemInterface
     */
    public function find($id)
    {
        return $this->_find($id, Competition::class);
    }
}
