<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests\Competition\Handler;

use Fightmaster\Trailrun\Competition\AccessCodeRepository;
use Fightmaster\Trailrun\Competition\Handler\CreateCompetition;
use Fightmaster\Trailrun\Tests\AccessCodeTrait;
use Fightmaster\Trailrun\Tests\TrailrunTestCase;

class CreateCompetitionTest extends TrailrunTestCase
{
    use AccessCodeTrait;

    protected function setUp()
    {
        parent::setUp();

        $this->truncateMongoDbCollections(['accessCode', 'competition']);
    }

    public function testCreateEmptyCompetition()
    {
        $accessCodes = $this->generateAccessCodes(4);
        $data = [
            'name' => 'Competition',
            'startTime' => time(),
            'endTime' => time(),
        ];

        $created = $this->getCreateCompetition()->handle($data);
        $this->assertNotEmpty($created);
        $this->assertEmpty($created->getCheckpoints());
        $this->assertEmpty($created->getTags());

        foreach ($accessCodes as $accessCode) {
            $this->assertTrue($created->hasAccess($accessCode->getCode(), $accessCode->getPass(), $accessCode->getAccess()));
        }
    }

    /**
     * @depends testCreateEmptyCompetition
     */
    public function testCreateCompetitionWithTags()
    {
        $this->generateAccessCodes(4);
        $data = [
            'name' => 'Competition',
            'startTime' => time(),
            'endTime' => time(),
            'tags' => ['М','Ж','18-39','40-49','50-59','Ultra','Trail']
        ];

        $created = $this->getCreateCompetition()->handle($data);
        $this->assertNotEmpty($created);
        $this->assertNotEmpty($created->getTags());
    }

    /**
     * @depends testCreateEmptyCompetition
     */
    public function testCreateCompetitionWithCheckpoints()
    {
        $this->generateAccessCodes(4);
        $data = [
            'name' => 'Competition',
            'startTime' => time(),
            'endTime' => time(),
            'checkpoints' => [['name' => 'start'], ['name' => '2 lap', 'sort' => 3], ['name' => 'finish', 'sort' => 50], ['name' => '1 lap', 'sort' => 1]],
        ];

        $created = $this->getCreateCompetition()->handle($data);
        $this->assertNotEmpty($created);
        $this->assertNotEmpty($created->getCheckpoints());
        $checkpoints = $created->getCheckpoints();
        $firstCheckPoint = array_pop($checkpoints);
        $this->assertEquals(50, $firstCheckPoint->getSort());
    }

    /**
     * @return CreateCompetition
     */
    protected function getCreateCompetition()
    {
        return $this->container[CreateCompetition::class];
    }

    /**
     * @return AccessCodeRepository
     */
    protected function getAccessCodeRepository()
    {
        return $this->container[AccessCodeRepository::class];
    }
}
