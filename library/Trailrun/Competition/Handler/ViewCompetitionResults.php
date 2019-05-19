<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CompetitionRepository;

class ViewCompetitionResults
{
    private $competitionRepository;

    private $checkpointResults;

    public function __construct(CompetitionRepository $competitionRepository, CheckpointResults $checkpointResults)
    {
        $this->competitionRepository = $competitionRepository;
        $this->checkpointResults = $checkpointResults;
    }

    public function handle($id)
    {
        $competition = $this->competitionRepository->find($id);
        $searchData = [
            'competitionId' => $id,
            'allTags' => ['Половинка (Н=500м)', 'Мужчины 18-39 лет'],
        ];
        $allResults = $this->checkpointResults->search($id, $searchData);

        $result = array_merge($allResults, ['competition' => $competition]);

        return $result;
    }
}
