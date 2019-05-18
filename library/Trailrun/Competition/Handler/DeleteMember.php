<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\MemberRepository;

class DeleteMember
{
    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @param MemberRepository $memberRepository
     */
    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    /**
     * @param $competitionId
     * @param $memberId
     * @return \MongoDB\DeleteResult
     */
    public function handle($competitionId, $memberId)
    {
        $member = $this->memberRepository->find($memberId);
        if ($member->getCompetitionId() !== $competitionId) {
            throw new \InvalidArgumentException();
        }

        return $this->memberRepository->delete($member);
    }
}
