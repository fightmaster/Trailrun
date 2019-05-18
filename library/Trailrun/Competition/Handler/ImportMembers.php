<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;


use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\Handler\Exception\CompetitionHasResultsData;
use Fightmaster\Trailrun\Competition\Handler\Exception\CompetitionNotFound;
use Fightmaster\Trailrun\Competition\Member;
use Fightmaster\Trailrun\Competition\MemberRepository;

class ImportMembers
{
    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @var CheckpointResultRepository
     */
    private $checkpointResultRepository;

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
     * @param $data
     * @return \MongoDB\InsertManyResult
     * @throws \Exception
     */
    public function handle($data)
    {
        $results = $this->checkpointResultRepository->findBy(['competitionId' => $data['competitionId']]);
        if (!empty($results)) {
            throw new CompetitionHasResultsData();
        }
        $this->memberRepository->deleteByCompetitionId($data['competitionId']);

        $competition = $this->competitionRepository->find($data['competitionId']);
        if (null === $competition) {
            throw new CompetitionNotFound();
        }

        $members = [];
        $csvData = $this->fetchData($data['filepath']);
        foreach ($csvData as $row) {
            $memberData = [
                'competitionId' => $data['competitionId'],
            ];
            foreach ($data['maps'] as $key => $column) {
                if (is_array($column)) {
                    foreach ($column as $columnKey => $columnValue) {
                        if (!empty($row[$columnValue])) {
                            $memberData[$key][$columnKey] = $row[$columnValue];
                        }
                    }
                } else {
                    if (!empty($row[$column])) {
                        $memberData[$key] = $row[$column];
                    }
                }
            }
            $members[] = Member::create($memberData);
        }

        return $this->memberRepository->insertCollection($members);
    }

    /**
     * @param string $filepath
     * @return array
     */
    private function fetchData(string $filepath): array
    {
        $csvOutputArray = array_map(function ($line) {
            return str_getcsv($line, ';');
        }, file($filepath));
        $membersData = $csvOutputArray;
        unset($membersData[0]);
        $membersData = array_values($membersData);

        return $membersData;
    }
}
