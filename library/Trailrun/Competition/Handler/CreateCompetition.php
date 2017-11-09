<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\AccessCodeRepository;
use Fightmaster\Trailrun\Competition\AccessList;
use Fightmaster\Trailrun\Competition\Competition;
use Fightmaster\Trailrun\Competition\CompetitionRepository;

class CreateCompetition
{
    /**
     * @var AccessCodeRepository
     */
    private $accessCodeRepository;

    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @param CompetitionRepository $competitionRepository
     * @param AccessCodeRepository $accessCodeRepository
     */
    public function __construct(CompetitionRepository $competitionRepository, AccessCodeRepository $accessCodeRepository)
    {
        $this->competitionRepository = $competitionRepository;
        $this->accessCodeRepository = $accessCodeRepository;
    }

    public function handle($data)
    {
        $data['tags'] = isset($data['tags']) ? $data['tags'] : [];
        $data['checkpoints'] = isset($data['checkpoints']) ? $data['checkpoints'] : [];
        $competition = Competition::create($data);

        $accessCodes = $this->accessCodeRepository->invokeCodes($competition->getId(), AccessList::getAccessList());
        foreach ($accessCodes as $accessCode) {
            $competition->addAccessCode($accessCode);
        }

        $this->competitionRepository->insert($competition);

        return $this->competitionRepository->find($competition->getId());

    }

}
