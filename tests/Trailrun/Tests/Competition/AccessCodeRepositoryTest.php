<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests\Competition;

use Fightmaster\Trailrun\Competition\AccessCode;
use Fightmaster\Trailrun\Competition\AccessCodeRepository;
use Fightmaster\Trailrun\Competition\AccessList;
use Fightmaster\Trailrun\Tests\AccessCodeTrait;
use Fightmaster\Trailrun\Tests\TrailrunTestCase;

class AccessCodeRepositoryTest extends TrailrunTestCase
{
    use AccessCodeTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->truncateMongoDbCollections(['accessCode']);
    }

    public function testFindUnusedCodes()
    {
        $accessCode = AccessCode::generate();
        $this->getAccessCodeRepository()->insert($accessCode);
        $fetched = $this->getAccessCodeRepository()->findUnusedCodes(1);

        $this->assertCount(1, $fetched);
        $this->assertEquals($accessCode, $fetched[0]);
    }

    /**
     * @depends testFindUnusedCodes
     */
    public function testInvokeCodes()
    {
        $this->generateAccessCodes(3);

        $invokeCodes = $this->getAccessCodeRepository()->invokeCodes(1, [AccessList::ACCESS_ADMIN, AccessList::ACCESS_GUEST]);

        foreach ($invokeCodes as $invokeCode) {
            $this->assertEquals(1, $invokeCode->getCompetitionId());
            $this->assertEquals(1, $invokeCode->isUsed());
        }

        $fetched = $this->getAccessCodeRepository()->findUnusedCodes(1);
        $this->assertCount(1, $fetched);
        $this->assertFalse($fetched[0]->isUsed());
    }

    /**
     * @depends testInvokeCodes
     */
    public function testFindByCompetition()
    {
        $this->generateAccessCodes(3);

        $invokeCodes = $this->getAccessCodeRepository()->invokeCodes(1, [AccessList::ACCESS_ADMIN, AccessList::ACCESS_GUEST]);

        $result = $this->getAccessCodeRepository()->findByCompetition(1);
        $this->assertCount(2, $result);

        $this->assertEquals($invokeCodes, $result);
    }

    public function testFindOneByCompetitionAndCode()
    {
        $this->generateAccessCodes(3);

        $invokeCodes = $this->getAccessCodeRepository()->invokeCodes(1, [AccessList::ACCESS_ADMIN, AccessList::ACCESS_GUEST]);

        $invokeCode = array_shift($invokeCodes);

        $result = $this->getAccessCodeRepository()->findOneByCompetitionAndCode(1, $invokeCode->getCode(), $invokeCode->getPass());
        $this->assertEquals($invokeCode, $result);
    }

    /**
     * @return AccessCodeRepository
     */
    protected function getAccessCodeRepository()
    {
        return $this->container[AccessCodeRepository::class];
    }

}
