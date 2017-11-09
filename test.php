<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

$array = [
    'gtr' => [
      'name' => 1,
      'sort' => 1,
    ],
    'adf' => [
        'name' => 2,
        'sort' => 5,
    ],
    'gdf' => [
        'name' => 3,
        'sort' => 3,
    ],
];
uasort($array, function ($a, $b)
{
    return $a['sort'] - $b['sort'];
});

var_dump($array);
