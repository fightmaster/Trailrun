<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;

class DeleteCheckpointResult
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
     * @param $checkpointResultId
     * @return \MongoDB\DeleteResult
     */
    public function handle($competitionId, $checkpointResultId)
    {
        $checkpointResult = $this->checkpointResultRepository->find($checkpointResultId);
        if ($checkpointResult->getCompetitionId() !== $competitionId) {
            throw new \InvalidArgumentException();
        }

        return $this->checkpointResultRepository->delete($checkpointResult);
    }
}
