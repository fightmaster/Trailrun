<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;

class EditCheckpointResult
{
    /**
     * @var CheckpointResultRepository
     */
    private $checkpointResultRepository;

    private $defineStartCheckpointResult;

    /**
     * EditCheckpointResult constructor.
     *
     * @param CheckpointResultRepository $checkpointResultRepository
     * @param DefineStartCheckpointResult $defineStartCheckpointResult
     */
    public function __construct(CheckpointResultRepository $checkpointResultRepository, DefineStartCheckpointResult $defineStartCheckpointResult)
    {
        $this->checkpointResultRepository = $checkpointResultRepository;
        $this->defineStartCheckpointResult = $defineStartCheckpointResult;
    }

    /**
     * @param array $data
     * @return CheckpointResult|\Fightmaster\Trailrun\StoreItemInterface
     */
    public function handle(array $data)
    {
        $checkpointResult = $this->checkpointResultRepository->find($data['id']);
        if ($checkpointResult->getCompetitionId() !== $data['competitionId']) {
            throw new \InvalidArgumentException();
        }

        if (!empty($data['clearTime'])) {
            /** @var CheckpointResult $startCheckpointResult */
            $startCheckpointResult = $this->defineStartCheckpointResult->handle($checkpointResult->getCompetitionId(), $checkpointResult->getMemberId());
            $data['time'] = $startCheckpointResult->getTime() + $data['clearTime'];
        }
        $checkpointResult->edit($data);

        $this->checkpointResultRepository->update($checkpointResult);

        return $this->checkpointResultRepository->find($checkpointResult->getId());
    }
}
