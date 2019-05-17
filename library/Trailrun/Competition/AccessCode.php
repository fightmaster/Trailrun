<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\StoreItemInterface;

class AccessCode implements StoreItemInterface
{
    private $code;
    private $pass;
    private $competitionId;
    private $access;
    private $used = false;

    private function __construct()
    {
    }

    public static function generate()
    {
        $accessCode = new self();
        $accessCode->code = self::generateCode();
        $accessCode->pass = self::generatePass();

        return $accessCode;
    }

    private static function generateCode()
    {
        return rand(0,9) . rand(0,9) . rand(0, 9) . rand(0,9) . rand(0, 9) . rand(0,9);
    }

    private static function generatePass()
    {
        return rand(0,9) . rand(0,9) . rand(0, 9);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'pass' => $this->pass,
            'competitionId' => $this->competitionId,
            'access' => $this->access,
            'used' => $this->used
        ];
    }

    /**
     * @param array $row
     * @return AccessCode
     */
    public static function fromArray(array $row): AccessCode
    {
        $accessCode = new self();
        $accessCode->code = $row['code'];
        $accessCode->pass = $row['pass'];
        $accessCode->competitionId = $row['competitionId'] ?? null;
        $accessCode->access = $row['access'] ?? null;
        $accessCode->used = $row['used'];

        return $accessCode;
    }

    /**
     * @return array
     */
    public function toStoreArray(): array
    {
        $toStore = $this->toArray();
        $toStore['_id'] = $toStore['code'];
        unset($toStore['code']);

        return $toStore;
    }

    public static function restore($row)
    {
        $row['code'] = $row['_id'];
        unset($row['_id']);

        return self::fromArray($row);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function useCode($competitionId, $access)
    {
        $this->competitionId = $competitionId;
        $this->access = $access;
        $this->used = true;
    }

    public function getId()
    {
        return $this->code;
    }

    public function getCode()
    {
        return $this->getId();
    }

    public function isUsed()
    {
        return (bool) $this->used;
    }

    public function getCompetitionId()
    {
        return $this->competitionId;
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function getAccess()
    {
        return $this->access;
    }

}
