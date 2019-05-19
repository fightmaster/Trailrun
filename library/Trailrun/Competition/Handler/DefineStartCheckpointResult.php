<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;

class DefineStartCheckpointResult
{
    /**
     * @var CheckpointResultRepository
     */
    private $checkpointResultRepository;

    /**
     * @param CheckpointResultRepository $checkpointResultRepository
     */
    public function __construct(CheckpointResultRepository $checkpointResultRepository)
    {
        $this->checkpointResultRepository = $checkpointResultRepository;
    }

    /**
     * @param $competitionId
     * @param $memberId
     * @return CheckpointResult|null
     */
    public function handle($competitionId, $memberId):?CheckpointResult
    {
        $checkpointResults = $this->checkpointResultRepository->findBy(['competitionId' => $competitionId, 'memberId' => $memberId]);

        uasort($checkpointResults, function ($checkpointResult1, $checkpointResult2) {
            return $checkpointResult1->getTime() > $checkpointResult2->getTime();
        });

        $startCheckpointResult = reset($checkpointResults);
        if ($startCheckpointResult) {
            return $startCheckpointResult;
        }

        return null;
    }
}
