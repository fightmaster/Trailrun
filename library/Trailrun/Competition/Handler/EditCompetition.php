<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CompetitionRepository;

class EditCompetition
{
    private $competitionRepository;

    public function __construct(CompetitionRepository $competitionRepository)
    {
        $this->competitionRepository = $competitionRepository;
    }

    public function handle($data)
    {
        $competition = $this->competitionRepository->find($data['id']);

        $competition->edit($data);

        $data['tags'] = !empty($data['tags']) ? $data['tags'] : [];
        $updatedTagInfo = $competition->alterTags($data['tags']);
        if (!empty($updatedTagInfo['deleted'])) {
            //check tags are unused in members
        }

        $updatedCheckpointInfo = $competition->alterCheckpoints($data['checkpoints']);
        if (!empty($updatedCheckpointInfo['deleted'])) {
            //check checkpoints are unused into results
        }

        $this->competitionRepository->update($competition);

        return $this->competitionRepository->find($competition->getId());
    }


}
