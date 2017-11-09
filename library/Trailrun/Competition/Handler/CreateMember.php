<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;


use Fightmaster\Trailrun\Competition\Member;
use Fightmaster\Trailrun\Competition\MemberRepository;

class CreateMember
{
    /**
     * @var MemberRepository
     */
    private $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    public function handle($data)
    {
        $data['tags'] = !empty($data['tags']) ? $data['tags'] : [];

        $member = Member::create($data);

        $this->memberRepository->insert($member);

        return $this->memberRepository->find($member->getId());
    }
}
