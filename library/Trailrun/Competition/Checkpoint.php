<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;

use Fightmaster\Trailrun\ItemInterface;
use Ramsey\Uuid\Uuid;

class Checkpoint implements ItemInterface
{
    private $id;
    private $name;
    private $sort;

    private function __construct()
    {
    }

    /**
     * @param string $competitionId
     * @param string $name
     * @param int $sort
     * @return Checkpoint
     */
    public static function create($competitionId, $name, $sort = 0)
    {
        $checkpoint = new self();
        $checkpoint->id = Uuid::uuid5(Uuid::NAMESPACE_X500, $competitionId . '_' . $name . '_' . time())->toString();
        $checkpoint->name = $name;
        $checkpoint->sort = $sort;

        return $checkpoint;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sort' => $this->sort,
        ];
    }

    /**
     * @param array $row
     * @return Checkpoint
     */
    public static function fromArray(array $row): Checkpoint
    {
        $checkpoint = new self();
        $checkpoint->id = $row['id'];
        $checkpoint->name = $row['name'];
        $checkpoint->sort = $row['sort'];

        return $checkpoint;
    }

    public static function fromArrayCollection($rows)
    {
        $items = [];
        foreach ($rows as $row) {
            $item = self::fromArray($row);
            $items[$item->getId()] = $item;
        }

        return $items;
    }

    public static function toArrayCollection($items)
    {
        $rows = [];
        foreach ($items as $item) {
            if (!$item instanceof ItemInterface) {
                throw new \InvalidArgumentException('Expects ItemInterface, got ' . get_class($item));
            }

            $rows[$item->getId()] = $item->toArray();
        }

        return $rows;
    }
}
