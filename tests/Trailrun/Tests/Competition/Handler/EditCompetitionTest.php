<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests\Competition\Handler;

use Fightmaster\Trailrun\Competition\AccessCode;
use Fightmaster\Trailrun\Competition\AccessCodeRepository;
use Fightmaster\Trailrun\Competition\AccessList;
use Fightmaster\Trailrun\Competition\Checkpoint;
use Fightmaster\Trailrun\Competition\Handler\CreateCompetition;
use Fightmaster\Trailrun\Competition\Handler\EditCompetition;
use Fightmaster\Trailrun\Tests\AccessCodeTrait;
use Fightmaster\Trailrun\Tests\TrailrunTestCase;

class EditCompetitionTest extends TrailrunTestCase
{
    use AccessCodeTrait;

    protected function setUp()
    {
        parent::setUp();

        $this->truncateMongoDbCollections(['accessCode', 'competition']);
    }

    public function testEditEmptyCompetition()
    {
        //move to generate competition without create handler
        $accessCodes = $this->generateAccessCodes(4);
        $data = [
            'name' => 'Competition',
            'startTime' => time(),
            'endTime' => time(),
            'tags' => ['М', 'Ж', '18'],
            'checkpoints' => [['name' => 'finish', 'sort' => 50], ['name' => 'start']],
        ];
        $created = $this->getCreateCompetition()->handle($data);

        $oldCheckpoints = Checkpoint::toArrayCollection($created->getCheckpoints());
        $firstCheckpoint = reset($oldCheckpoints);
        $firstCheckpoint['name'] = 'General Start';

        $data = [
            'id' => $created->getId(),
            'name' => 'Edit Name',
            'startTime' => time() + 10,
            'endTime' => time() + 15,
            'tags' => ['М', 'Ж', '40'],
            'checkpoints' => [['name' => '1 lap', 'sort' => 30], $firstCheckpoint]
        ];

        $edited = $this->getEditCompetition()->handle($data);

        $this->assertEquals($data['name'], $edited->getName());
        $this->assertEquals($data['startTime'], $edited->getStartTime());
        $this->assertEquals($data['endTime'], $edited->getEndTime());

        $this->assertNotEmpty($edited);
        $this->assertEquals($data['tags'], $edited->getTags());
        $checkpoints = $edited->getCheckpoints();
        $this->assertCount(2, $checkpoints);
        $firstCheckpoint = array_shift($checkpoints);
        $this->assertEquals('General Start', $firstCheckpoint->getName());
        $secondCheckpoint = array_shift($checkpoints);
        $this->assertEquals('1 lap', $secondCheckpoint->getName());
        $this->assertNotEmpty($secondCheckpoint->getId());

        foreach ($accessCodes as $accessCode) {
            $this->assertTrue($created->hasAccess($accessCode->getCode(), $accessCode->getPass(), $accessCode->getAccess()));
        }
    }

    /**
     * @return CreateCompetition
     */
    protected function getCreateCompetition()
    {
        return $this->container[CreateCompetition::class];
    }

    /**
     * @return EditCompetition
     */
    protected function getEditCompetition()
    {
        return $this->container[EditCompetition::class];
    }

    /**
     * @return AccessCodeRepository
     */
    protected function getAccessCodeRepository()
    {
        return $this->container[AccessCodeRepository::class];
    }
}
