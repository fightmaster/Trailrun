<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Tests;

use Fightmaster\Trailrun\Competition\AccessCode;
use Fightmaster\Trailrun\Competition\AccessCodeRepository;

trait AccessCodeTrait
{
    /**
     * @return AccessCodeRepository
     */
    abstract protected function getAccessCodeRepository();

    /**
     * @param int $numbers
     * @return AccessCode[]
     */
    protected function generateAccessCodes($numbers = 4)
    {
        $accessCodes = [];
        for ($i = 0; $i < $numbers; $i++) {
            $accessCode = AccessCode::generate();
            $this->getAccessCodeRepository()->insert($accessCode);
            $accessCodes[] = $accessCode;
        }

        return $accessCodes;
    }
}
