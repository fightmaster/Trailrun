<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class ListMembers
{
    private $competitionRepository;

    private $memberRepository;

    public function __construct(CompetitionRepository $competitionRepository, MemberRepository $memberRepository)
    {
        $this->competitionRepository = $competitionRepository;
        $this->memberRepository = $memberRepository;
    }

    public function handle($competitionId)
    {
        $competition =  $this->competitionRepository->find($competitionId);
        $members = $this->memberRepository->findBy(['competitionId' => $competitionId]);

        uasort($members, function ($member1, $member2) {
            if ($member1->getLastName() == $member2->getLastName()) {
                return $member1->getFirstName() > $member2->getFirstName();
            }

            return $member1->getLastName() > $member2->getLastName();
        });

        return [
            'competition' => $competition,
            'members' => $members
        ];
    }
}
