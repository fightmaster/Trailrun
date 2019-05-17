<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests\Competition;

use Fightmaster\Trailrun\Competition\CheckpointResultRepository;
use Fightmaster\Trailrun\Competition\CheckpointResult;
use Fightmaster\Trailrun\Tests\TrailrunTestCase;
use MongoDB\InsertManyResult;

class CheckpointResultRepositoryTest extends TrailrunTestCase
{
    /**
     * @var string
     */
    private $competitionId1;

    /**
     * @var string
     */
    private $competitionId2;

    /**
     * @var array
     */
    private $times = [];

    /**
     * @var string
     */
    private $memberId1;

    /**
     * @var string
     */
    private $memberId2;

    /**
     * @var string
     */
    private $memberId3;

    /**
     * @var string
     */
    private $checkPointId1;

    /**
     * @var string
     */
    private $checkPointId2;

    /**
     * @var string
     */
    private $checkPointId3;

    /**
     * @var string
     */
    private $checkPointId4;

    public function testFind()
    {
        $insertedItems = $this->generateCheckpointResults();
        $ids = $insertedItems->getInsertedIds();
        $checkpointResult1 = $this->getCheckpointResultRepository()->find($ids[0]);
        $this->assertInstanceOf(CheckpointResult::class, $checkpointResult1);
    }

    public function testFindBy()
    {
        $this->generateCheckpointResults();

        $checkpointResults = $this->getCheckpointResultRepository()->findBy([
            'competitionId' => $this->competitionId1
        ]);
        foreach ($checkpointResults as $checkpointResult) {
            $this->assertEquals($this->competitionId1, $checkpointResult->getCompetitionId());
        }

        $checkpointResults = $this->getCheckpointResultRepository()->findBy([
            'competitionId' => $this->competitionId1,
            'checkpointIds' => [$this->checkPointId1]
        ]);
        foreach ($checkpointResults as $checkpointResult) {
            $this->assertEquals($this->competitionId1, $checkpointResult->getCompetitionId());
            $this->assertEquals($this->checkPointId1, $checkpointResult->getCheckpointId());
        }

        $checkpointResults = $this->getCheckpointResultRepository()->findBy([
            'competitionId' => $this->competitionId1,
            'checkpointIds' => [$this->checkPointId1, $this->checkPointId2]
        ]);
        foreach ($checkpointResults as $checkpointResult) {
            $this->assertEquals($this->competitionId1, $checkpointResult->getCompetitionId());
            $this->assertTrue(in_array($checkpointResult->getCheckpointId(), [$this->checkPointId1, $this->checkPointId2]));
        }

        $checkpointResults = $this->getCheckpointResultRepository()->findBy([
            'competitionId' => $this->competitionId1,
            'memberId' => $this->memberId1
        ]);
        foreach ($checkpointResults as $checkpointResult) {
            $this->assertEquals($this->competitionId1, $checkpointResult->getCompetitionId());
            $this->assertEquals($this->memberId1, $checkpointResult->getMemberId());
            $this->assertTrue(in_array($checkpointResult->getCheckpointId(), [$this->checkPointId1, $this->checkPointId2]));
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->truncateMongoDbCollections(['checkpointResult']);
    }

    /**
     * @return CheckpointResultRepository
     */
    private function getCheckpointResultRepository(): CheckpointResultRepository
    {
        return $this->container[CheckpointResultRepository::class];
    }

    /**
     * @return InsertManyResult
     */
    private function generateCheckpointResults(): InsertManyResult
    {
        $this->competitionId1 = $this->generateUuid1();
        $this->competitionId2 = $this->generateUuid1();

        $this->memberId1 = $this->generateUuid1();
        $this->memberId2 = $this->generateUuid1();
        $this->memberId3 = $this->generateUuid1();

        $time = time();
        $this->times = [];
        for ($i = 0; $i < 6; $i++) {
            $this->times[] = $time + $i;
        }

        $this->checkPointId1 = $this->generateUuid1();
        $this->checkPointId2 = $this->generateUuid1();
        $this->checkPointId3 = $this->generateUuid1();
        $this->checkPointId4 = $this->generateUuid1();

        $checkPointResults = [];
        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId1, $this->times[0], $this->checkPointId1);
        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId1, $this->times[1], $this->checkPointId2);

        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId2, $this->times[2], $this->checkPointId1);
        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId2, $this->times[3], $this->checkPointId2);

        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId3, $this->times[4], $this->checkPointId1);
        $checkPointResults[] = CheckpointResult::create($this->competitionId1, $this->memberId3, $this->times[5], $this->checkPointId2);


        $checkPointResults[] = CheckpointResult::create($this->competitionId2, $this->memberId1, $this->times[0], $this->checkPointId3);
        $checkPointResults[] = CheckpointResult::create($this->competitionId2, $this->memberId1, $this->times[1], $this->checkPointId4);

        $checkPointResults[] = CheckpointResult::create($this->competitionId2, $this->memberId2, $this->times[2], $this->checkPointId3);
        $checkPointResults[] = CheckpointResult::create($this->competitionId2, $this->memberId2, $this->times[3], $this->checkPointId4);

        return $this->getCheckpointResultRepository()->insertCollection($checkPointResults);
    }

}
