<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\User;

use Fightmaster\Trailrun\StoreItemInterface;
use Ramsey\Uuid\Uuid;

class User implements StoreItemInterface
{
    private $id;
    private $info;

    private function __construct()
    {
    }

    /**
     * @param array $data
     * @return User
     */
    public static function create($data): User
    {
        $member = new self();
        $member->id = Uuid::uuid1()->toString();
        $member->info['firstName'] = $data['firstName'];
        $member->info['lastName'] = $data['lastName'];

        $member->info['dob'] = $data['dob'];
        if (!empty($data['clubName'])) {
            $member->info['clubName'] = $data['clubName'];
        }

        if (!empty($data['city'])) {
            $member->info['city'] = $data['city'];
        }

        if (!empty($data['performanceIndex'])) {
            $member->info['performanceIndex'] = $data['performanceIndex'];
        }

        return $member;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'info' => $this->info,
        ];
    }

    /**
     * @param array $row
     * @return User
     */
    public static function fromArray(array $row): User
    {
        $user = new self();
        $user->id = $row['id'];
        $user->info = $row['info'];

        return $user;
    }

    /**
     * @return array
     */
    public function toStoreArray(): array
    {
        $toStore = $this->toArray();
        $toStore['_id'] = $toStore['id'];
        unset($toStore['id']);

        return $toStore;
    }

    /**
     * @param array $row
     * @return User
     */
    public static function restore(array $row): User
    {
        $row['id'] = $row['_id'];
        unset($row['_id']);

        return self::fromArray($row);
    }


}
