<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition\Handler;

use Fightmaster\Trailrun\Competition\MemberRepository;

class EditMember
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
        $member = $this->memberRepository->find($data['id']);

        $member->edit($data);

        $data['tags'] = !empty($data['tags']) ? $data['tags'] : [];
        $updatedTagInfo = $member->alterTags($data['tags']);
        if (!empty($updatedTagInfo['deleted'])) {
        }

        //check results
        $changeNumberAllowed = true;
        if ($changeNumberAllowed && !empty($data['number'])) {
            $member->changeNumber($data['number']);
        }

        $this->memberRepository->update($member);

        return $this->memberRepository->find($member->getId());
    }
}
