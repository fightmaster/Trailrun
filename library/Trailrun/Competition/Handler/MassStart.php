<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;
use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class MassStart
{
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    private $checkpointResultRepository;

    /**
     * @param CompetitionRepository $competitionRepository
     * @param MemberRepository $memberRepository
     * @param CheckpointResultRepository $checkpointResultRepository
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        MemberRepository $memberRepository,
        CheckpointResultRepository $checkpointResultRepository
    )
    {
        $this->competitionRepository = $competitionRepository;
        $this->memberRepository = $memberRepository;
        $this->checkpointResultRepository = $checkpointResultRepository;
    }

    /**
     * @param array $data
     */
    public function handle(array $data)
    {
        $competition = $this->competitionRepository->find($data['competitionId']);
        $members = $this->memberRepository->findBy($data);

        $checkpoints = $competition->getCheckpoints();
        $startCheckpoint = reset($checkpoints);

        $startTime = time();
        $checkpointResults = [];
        foreach ($members as $member) {
            $checkpointResults[] = CheckpointResult::create($competition->getId(), $member->getId(), $startTime, $startCheckpoint->getId());
        }
        if (empty($checkpointResults)) {
            return;
        }
        $this->checkpointResultRepository->insertCollection($checkpointResults);
    }
}
