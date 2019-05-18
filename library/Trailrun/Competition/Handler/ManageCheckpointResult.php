<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;
use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class ManageCheckpointResult
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


    /**
     * @param $competitionId
     */
    public function startOne($competitionId)
    {
        $competition = $this->competitionRepository->find($competitionId);
        $checkpoints = $competition->getCheckpoints();
        $startCheckpoint = reset($checkpoints);
        $time = time();
        $searchData = [
            'competitionId' => $competitionId,
            'allTags' => ['Основная (Н=1000м)', 'Мужчины 18-39 лет'],
        ];
        $members = $this->memberRepository->findBy($searchData);
        $checkpointResults = [];
        foreach ($members as $member) {
            $checkpointResults[] = CheckpointResult::create($member->getCompetitionId(), $member->getId(), $time, $startCheckpoint->getId());
        }
        if (empty($checkpointResults)) {
            return;
        }
        $this->checkpointResultRepository->insertCollection($checkpointResults);
    }

    /**
     * @param $competitionId
     */
    public function startTwo($competitionId)
    {
        $competition = $this->competitionRepository->find($competitionId);
        $checkpoints = $competition->getCheckpoints();
        $startCheckpoint = reset($checkpoints);
        $time = time();
        $searchData = [
            'competitionId' => $competitionId,
            'allTags' => ['Основная (Н=1000м)', 'Мужчины 18-39 лет'],
        ];
        $firstMembers = $this->memberRepository->findBy($searchData);
        $allMembers = $this->memberRepository->findBy(['competitionId' => $competitionId]);
        $members = [];
        foreach ($allMembers as $member) {
            foreach ($firstMembers as $firstMember) {
                if ($member->getId() == $firstMember->getId()) {
                    continue 2;
                }
            }
            $members[] = $member;
        }

        $checkpointResults = [];
        foreach ($members as $member) {
            $checkpointResults[] = CheckpointResult::create($member->getCompetitionId(), $member->getId(), $time, $startCheckpoint->getId());
        }
        if (empty($checkpointResults)) {
            return;
        }
        $this->checkpointResultRepository->insertCollection($checkpointResults);
    }

    /**
     * @param array $data
     * @return \MongoDB\InsertManyResult|void
     */
    public function addByNumber(array $data)
    {
        $members = $this->memberRepository->findBy($data);
        $data['checkpointId'] = $data['checkpointId'] ?? null;
        $time = time();
        $checkpointResults = [];
        foreach ($members as $member) {
            $checkpointResults[] = CheckpointResult::create($member->getCompetitionId(), $member->getId(), $time, $data['checkpointId']);
        }
        if (empty($checkpointResults)) {
            return;
        }

        return $this->checkpointResultRepository->insertCollection($checkpointResults);
    }
}
