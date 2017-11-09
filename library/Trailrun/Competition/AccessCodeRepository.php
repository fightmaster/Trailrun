<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\Database\MongoDB\BaseRepository;

class AccessCodeRepository extends BaseRepository
{
    protected function getCollectionName()
    {
        return 'accessCode';
    }

    /**
     * @param $competitionId
     * @param $listAccess
     * @return AccessCode[]
     */
    public function invokeCodes($competitionId, $listAccess)
    {
        $countCodes = count($listAccess);
        if ($countCodes == 0) {
            throw new \InvalidArgumentException('Access list is required');
        }

        // invoke codes
        $availableCodes = $this->findUnusedCodes($countCodes);

        if (count($availableCodes) < $countCodes) {
            throw new \InvalidArgumentException('There are no available codes. CompetitionId: ' . $competitionId . ', accessCodes: ' . print_r($listAccess, 1));
        }

        $result = [];
        foreach ($listAccess as $access) {
            $availableCode = array_shift($availableCodes);
            $availableCode->useCode($competitionId, $access);
            $this->update($availableCode);
            $result[] = $availableCode;
        }

        return $result;
    }

    /**
     * @param int $count
     * @return AccessCode[]
     */
    public function findUnusedCodes($count)
    {
        $cursor = $this->collection->find(['used' => false], ['limit' => $count, 'typeMap' => $this->getTypeMap()]);

        $rows = $cursor->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[] = AccessCode::restore($row);
        }

        return $result;
    }

    public function findByCompetition($competitionId)
    {
        $cursor = $this->collection->find(['competitionId' => $competitionId], ['typeMap' => $this->getTypeMap()]);

        if (empty($cursor)) {
            return [];
        }

        $rows = $cursor->toArray();

        $result = [];
        foreach ($rows as $row) {
            $result[] = AccessCode::restore($row);
        }

        return $result;

    }

    public function findOneByCompetitionAndCode($competitionId, $code, $pass)
    {
        $row = $this->collection->findOne(['_id' => $code, 'competitionId' => $competitionId, 'pass' => $pass], ['typeMap' => $this->getTypeMap()]);

        if (empty($row)) {
            return null;
        }

        return AccessCode::restore($row);

    }
}
