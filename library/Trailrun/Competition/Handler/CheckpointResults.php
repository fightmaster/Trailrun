<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;
use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class CheckpointResults
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
     * @param $competitionId
     * @param int $limit
     * @return array
     */
    public function last($competitionId, $limit = 10)
    {
        $checkpointResults = $this->checkpointResultRepository->findBy(['competitionId' => $competitionId]);

        uasort($checkpointResults, function ($checkpointResult1, $checkpointResult2) {
            return $checkpointResult1->getTime() < $checkpointResult2->getTime();
        });
        /** @var CheckpointResult[] $checkpointResults */
        $checkpointResults = array_chunk($checkpointResults, $limit);
        if (!empty($checkpointResults)) {
            $checkpointResults = reset($checkpointResults);
        }
        $members = [];
        foreach ($checkpointResults as $checkpointResult) {
            $members[$checkpointResult->getMemberId()] = $this->memberRepository->find($checkpointResult->getMemberId());
        }

        return [
            'checkpointResults' => $checkpointResults,
            'members' => $members
        ];
    }

    /**
     * @param $competitionId
     * @return array
     */
    public function all($competitionId)
    {
        $checkpointResults = $this->checkpointResultRepository->findBy(['competitionId' => $competitionId]);

        uasort($checkpointResults, function ($checkpointResult1, $checkpointResult2) {
            return $checkpointResult1->getTime() > $checkpointResult2->getTime();
        });

        $resultsByMember = [];
        foreach ($checkpointResults as $checkpointResult) {
            if (!isset($resultsByMember[$checkpointResult->getMemberId()])) {
                $resultsByMember[$checkpointResult->getMemberId()] = [
                    'results' => [],
                    'startTime' => $checkpointResult->getTime(),
                    'count' => 0
                ];
            }
            $resultsByMember[$checkpointResult->getMemberId()]['results'][] = $checkpointResult;
            $resultsByMember[$checkpointResult->getMemberId()]['count']++;
        }


        uasort($resultsByMember, function ($resultsByMember1, $resultsByMember2) {
            if ($resultsByMember1['count'] == $resultsByMember2['count']) {
                $last1 = last($resultsByMember1['results']);
                $last2 = last($resultsByMember2['results']);

                return ($last1->getTime() - $resultsByMember1['startTime']) > ($last2->getTime() - $resultsByMember2['startTime']);
            }

            return $resultsByMember1['count'] < $resultsByMember2['count'];
        });

        /** @var CheckpointResult[] $checkpointResults */
        $members = $this->memberRepository->findBy(['competitionId' => $competitionId]);
        $membersById = [];
        foreach ($members as $member) {
            $membersById[$member->getId()] = $member;
        }

        return [
            'results' => $resultsByMember,
            'members' => $membersById
        ];
    }

    /**
     * @param $competitionId
     * @param array $filter
     * @return array
     */
    public function search($competitionId, array $filter)
    {
        /** @var CheckpointResult[] $checkpointResults */
        $members = $this->memberRepository->findBy($filter);
        $membersById = [];
        foreach ($members as $member) {
            $membersById[$member->getId()] = $member;
        }

        $checkpointResults = $this->checkpointResultRepository->findBy([
            'competitionId' => $competitionId,
            'memberIds' => array_keys($membersById)
        ]);

        uasort($checkpointResults, function ($checkpointResult1, $checkpointResult2) {
            return $checkpointResult1->getTime() > $checkpointResult2->getTime();
        });

        $resultsByMember = [];
        foreach ($checkpointResults as $checkpointResult) {
            if (!isset($resultsByMember[$checkpointResult->getMemberId()])) {
                $resultsByMember[$checkpointResult->getMemberId()] = [
                    'results' => [],
                    'startTime' => $checkpointResult->getTime(),
                    'count' => 0
                ];
            }
            $resultsByMember[$checkpointResult->getMemberId()]['results'][] = $checkpointResult;
            $resultsByMember[$checkpointResult->getMemberId()]['count']++;
        }


        uasort($resultsByMember, function ($resultsByMember1, $resultsByMember2) {
            if ($resultsByMember1['count'] == $resultsByMember2['count']) {
                $last1 = last($resultsByMember1['results']);
                $last2 = last($resultsByMember2['results']);

                return ($last1->getTime() - $resultsByMember1['startTime']) > ($last2->getTime() - $resultsByMember2['startTime']);
            }

            return $resultsByMember1['count'] < $resultsByMember2['count'];
        });

        return [
            'results' => $resultsByMember,
            'members' => $membersById
        ];
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
                if ($members->getId() == $firstMember->getId()) {
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
