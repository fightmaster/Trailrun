<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\CompetitionRepository;
use Fightmaster\Trailrun\Competition\MemberRepository;

class ViewMember
{
    private $competitionRepository;

    private $memberRepository;

    public function __construct(CompetitionRepository $competitionRepository, MemberRepository $memberRepository)
    {
        $this->competitionRepository = $competitionRepository;
        $this->memberRepository = $memberRepository;
    }

    public function handle($competitionId, $memberId)
    {
        $member = $this->memberRepository->find($memberId);
        if ($member->getCompetitionId() !== $competitionId) {
            throw new \InvalidArgumentException();
        }
        $competition =  $this->competitionRepository->find($competitionId);

        return [
            'competition' => $competition,
            'member' => $member
        ];
    }
}
