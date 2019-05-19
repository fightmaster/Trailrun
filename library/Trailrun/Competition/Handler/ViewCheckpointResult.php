<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class ViewCheckpointResult
{
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var CheckpointResultRepository
     */
    private $checkpointResultRepository;

    /**
     * @var DefineStartCheckpointResult
     */
    private $defineStartCheckpointResult;

    /**
     * @param CompetitionRepository $competitionRepository
     * @param CheckpointResultRepository $checkpointResultRepository
     * @param MemberRepository $memberRepository
     * @param DefineStartCheckpointResult $defineStartCheckpointResult
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        CheckpointResultRepository $checkpointResultRepository,
        MemberRepository $memberRepository,
        DefineStartCheckpointResult $defineStartCheckpointResult
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->memberRepository = $memberRepository;
        $this->checkpointResultRepository = $checkpointResultRepository;
        $this->defineStartCheckpointResult = $defineStartCheckpointResult;
    }

    /**
     * @param $competitionId
     * @param $checkpointResultId
     * @return array
     */
    public function handle($competitionId, $checkpointResultId)
    {
        $checkpointResult = $this->checkpointResultRepository->find($checkpointResultId);
        if ($checkpointResult->getCompetitionId() !== $competitionId) {
            throw new \InvalidArgumentException();
        }
        $member = $this->memberRepository->find($checkpointResult->getMemberId());
        $competition =  $this->competitionRepository->find($competitionId);

        return [
            'startCheckpointResult' => $this->defineStartCheckpointResult->handle($checkpointResult->getCompetitionId(), $checkpointResult->getMemberId()),
            'checkpointResult' => $checkpointResult,
            'competition' => $competition,
            'member' => $member
        ];
    }
}
