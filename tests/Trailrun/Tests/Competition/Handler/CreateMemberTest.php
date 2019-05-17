<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests\Competition\Handler;

use Fightmaster\Trailrun\Competition\AccessCodeRepository;
use Fightmaster\Trailrun\Competition\Handler\CreateCompetition;
use Fightmaster\Trailrun\Competition\Handler\CreateMember;
use Fightmaster\Trailrun\Tests\AccessCodeTrait;
use Fightmaster\Trailrun\Tests\TrailrunTestCase;

class CreateMemberTest extends TrailrunTestCase
{
    use AccessCodeTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->truncateMongoDbCollections(['accessCode', 'competition', 'member']);
    }

    public function testCreateMember()
    {
        $accessCodes = $this->generateAccessCodes(4);
        $data = [
            'name' => 'Competition',
            'startTime' => time(),
            'endTime' => time(),
            'tags' => ['М', 'Ж', '18'],
        ];

        $competition = $this->getCreateCompetition()->handle($data);

        $data = [
            'competitionId' => $competition->getId(),
            'tags' => ['М', '18'],
            'firstName' => 'Name',
            'lastName' => 'LastName',
            'city' => 'Town',
            'clubName' => 'ClubName',
            'number' => '111'

        ];
        $member = $this->getCreateMember()->handle($data);

        $this->assertEquals(['М', '18'], $member->getTags());
        $this->assertEquals('Name', $member->getFirstName());
        $this->assertEquals('LastName', $member->getLastName());
        $this->assertEquals('ClubName', $member->getClubName());
        $this->assertEquals('Town', $member->getCity());
        $this->assertEquals('111', $member->getNumber());
        $this->assertNotEmpty($member->getCodeNumber());
    }

    /**
     * @return CreateCompetition
     */
    protected function getCreateCompetition()
    {
        return $this->container[CreateCompetition::class];
    }

    /**
     * @return CreateMember
     */
    protected function getCreateMember()
    {
        return $this->container[CreateMember::class];
    }

    /**
     * @return AccessCodeRepository
     */
    protected function getAccessCodeRepository()
    {
        return $this->container[AccessCodeRepository::class];
    }
}
