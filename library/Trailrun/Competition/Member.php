<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;


use Fightmaster\Trailrun\StoreItemInterface;
use Ramsey\Uuid\Uuid;

class Member implements StoreItemInterface
{
    use ManageTagsTrait;

    /**
     * @var string
     */
    private $id;
    private $competitionId;
    private $info;
    private $codeNumber;
    private $number;

    private function __construct()
    {
    }

    /**
     * @param $data
     * @return Member
     * @throws \Exception
     */
    public static function create($data)
    {
        $member = new self();
        $member->id = Uuid::uuid1()->toString();
        $member->competitionId = $data['competitionId'];
        $member->info['firstName'] = $data['firstName'];
        $member->info['lastName'] = $data['lastName'];
        if (!empty($data['dob'])) {
            $member->info['dob'] = $data['dob'];
        }
        if (!empty($data['clubName'])) {
            $member->info['clubName'] = $data['clubName'];
        }

        if (!empty($data['city'])) {
            $member->info['city'] = $data['city'];
        }
        if (!empty($data['gender'])) {
            $member->info['gender'] = $data['gender'];
        }
        if (!empty($data['phone'])) {
            $member->info['phone'] = $data['phone'];
        }
        if (!empty($data['email'])) {
            $member->info['email'] = $data['email'];
        }
        if (!empty($data['registrationDate'])) {
            $member->info['registrationDate'] = $data['registrationDate'];
        }

        $member->tags = $data['tags'];

        if (!empty($data['number'])) {
            $member->changeNumber($data['number']);
        }

        return $member;
    }

    /**
     * @param array $data
     */
    public function edit($data)
    {
        if (!empty($data['firstName'])) {
            $this->info['firstName'] = $data['firstName'];
        }
        if (!empty($data['lastName'])) {
            $this->info['lastName'] = $data['lastName'];
        }
        if (!empty($data['dob'])) {
            $this->info['dob'] = $data['dob'];
        }
        if (!empty($data['clubName'])) {
            $this->info['clubName'] = $data['clubName'];
        }
        if (!empty($data['city'])) {
            $this->info['city'] = $data['city'];
        }
        if (!empty($data['gender'])) {
            $this->info['gender'] = $data['gender'];
        }
        if (!empty($data['phone'])) {
            $this->info['phone'] = $data['phone'];
        }
        if (!empty($data['email'])) {
            $this->info['email'] = $data['email'];
        }
        if (!empty($data['registrationDate'])) {
            $this->info['registrationDate'] = $data['registrationDate'];
        }
    }

    public function changeNumber($number)
    {
        if (empty($number)) {
            $this->number = null;
            $this->codeNumber = null;

            return;
        }
        if ($this->number == $number) {
            return;
        }
        $this->number = $number;
        $this->codeNumber = self::generateCodeNumber($number);
    }

    private static function generateCodeNumber($number)
    {
        return Uuid::uuid5(Uuid::NAMESPACE_X500, $number)->toString();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getCompetitionId()
    {
        return $this->competitionId;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'competitionId' => $this->competitionId,
            'info' => $this->info,
            'number' => $this->number,
            'codeNumber' => $this->codeNumber,
            'tags' => $this->tags,
        ];
    }

    /**
     * @param array $row
     * @return Member
     */
    public static function fromArray(array $row): Member
    {
        $member = new self();
        $member->id = $row['id'];
        $member->competitionId = $row['competitionId'];
        $member->info = $row['info'];
        $member->number = $row['number'];
        $member->codeNumber = $row['codeNumber'];
        $member->tags = $row['tags'];

        return $member;
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
     * @return Member
     */
    public static function restore(array $row): Member
    {
        $row['id'] = $row['_id'];
        unset($row['_id']);

        return self::fromArray($row);
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function getCodeNumber()
    {
        return $this->codeNumber;
    }

    public function getFirstName()
    {
        return !empty($this->info['firstName']) ? $this->info['firstName'] : null;
    }

    public function getLastName()
    {
        return !empty($this->info['lastName']) ? $this->info['lastName'] : null;
    }

    public function getCity()
    {
        return !empty($this->info['city']) ? $this->info['city'] : null;
    }

    public function getClubName()
    {
        return !empty($this->info['clubName']) ? $this->info['clubName'] : null;
    }

    public function getDob()
    {
        return !empty($this->info['dob']) ? $this->info['dob'] : null;
    }

    public function getPhone()
    {
        return !empty($this->info['phone']) ? $this->info['phone'] : null;
    }

    public function getEmail()
    {
        return !empty($this->info['email']) ? $this->info['email'] : null;
    }

    public function getRegistrationDate()
    {
        return !empty($this->info['registrationDate']) ? $this->info['registrationDate'] : null;
    }
}
