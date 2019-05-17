<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

require_once(dirname(__FILE__).'/../init.php');

echo PHP_EOL;

$timeColumn = 7;
$pointsColumn = 10;
$secondsColumn = 11;
$fileNameSumma = __DIR__ . '/summa.csv';
$fileNames = [__DIR__ . '/1.csv', __DIR__ . '/2.csv', __DIR__ . '/3.csv', __DIR__ . '/4.csv', __DIR__ . '/5.csv', __DIR__ . '/6.csv', __DIR__ . '/7.csv', __DIR__ . '/8.csv', __DIR__ . '/8_1.csv'];
$rangs = [1,1,1,1.5,1,1.5,1.5,1.5,1.5];
$numberOfStages = count($fileNames);
$positions = [
    'abs' => 0
];

$summa = [];

$numberOfStage = 0;
foreach ($fileNames as $etap => $fileName) {
    $numberOfStage++;
    $fileData = file($fileName);
    $countMembers = count($fileData) - 1;
    $liderTime = 10000000;
    $stageData= [];
    for ($i = 1; $i <= $countMembers; $i++) {
        $member = explode(';', $fileData[$i]);
        $timeArray = explode(':', $member[$timeColumn]);
        $time = 0;
        if (!empty($timeArray[1])) {
            if (count($timeArray) == 2) {
                array_unshift($timeArray, 0);
            }
            $time = $timeArray[0] * 3600 + $timeArray[1] * 60 + $timeArray[2];

            if ($time < $liderTime) {
                $liderTime = $time;
            }
        }
        array_push($member, $time);
        $stageData[] = $member;
    }
    foreach ($stageData as $key => $data) {
        $points = 200;
        if (!empty($data[$secondsColumn])) {
            $points = 1000 * $rangs[$etap] * round($liderTime / $data[$secondsColumn], 3);
        }
        $stageData[$key][$pointsColumn] = $points;
        if (!isset($summa[$stageData[$key][0]])) {
            //ФИО;№;ЛЕТ;ПОЛ;ГР;КОМАНДА;ГОРОД;ФИНИШ;М;М ГР;ОЧКИ
            $stageData[$key][0] = mb_strtolower($stageData[$key][0]);
            $stageData[$key][0] = mb_convert_case($stageData[$key][0], MB_CASE_TITLE, "UTF-8");
            $summa[$stageData[$key][0]] = [
                $stageData[$key][0],//ФИО
                $stageData[$key][2],//ЛЕТ
                $stageData[$key][3],//ПОЛ
                $stageData[$key][4],//ГР
                $stageData[$key][5],//КОМАНДА
                $stageData[$key][6],//ГОРОД
            ];
            for ($j = 0; $j < $numberOfStages; $j++) {
                $summa[$stageData[$key][0]][$j + 7] = 0;
            }
            $summa[$stageData[$key][0]][$numberOfStages + 8] = '0/0';
            $summa[$stageData[$key][0]][$numberOfStages + 9] = 0;
            $summa[$stageData[$key][0]][$numberOfStages + 10] = 1000;
            $summa[$stageData[$key][0]][$numberOfStages + 11] = 1000;
        }
        $summa[$stageData[$key][0]][$numberOfStage + 6] = $points;
    }
    foreach ($stageData as $key => $data) {
        $pointsByStages = [];
        for ($i = 1; $i <= $numberOfStages; $i++) {
            $pointsByStages[] = $summa[$stageData[$key][0]][$i + 6];
        }
        rsort($pointsByStages);
        $countStarts = 0;
        $countFinish = 0;
        foreach ($pointsByStages as $points) {
            if ($points > 0) {
                $countStarts++;
            }
            if ($points > 200) {
                $countFinish++;
            }
        }

        $best6Points = array_chunk($pointsByStages, 6);
        $summa[$stageData[$key][0]][$numberOfStages + 8] = $countStarts . '/' . $countFinish;
        $summa[$stageData[$key][0]][$numberOfStages + 9] = array_sum($best6Points[0]);
    }

    for ($i = 0; $i < $countMembers; $i++) {
        unset($stageData[$i][$secondsColumn]);
        $hasPoints = $stageData[$i][$pointsColumn] > 0;
        $member = implode(';', $stageData[$i]);
        if ($hasPoints) {
            $member .= PHP_EOL;
        }
        $fileData[$i + 1] = $member;
    }
    file_put_contents($fileName, $fileData);
}
uasort($summa, function ($a, $b) use ($numberOfStages) {
    return ($a[$numberOfStages + 9] < $b[$numberOfStages + 9]);
});
foreach ($summa as $i => $row) {
    if (!isset($positions[$row[3]])) {
        $positions[$row[3]] = 0;
    }
    $positions['abs']++;
    $positions[$row[3]]++;
    $summa[$i][$numberOfStages + 10] = $positions['abs'];
    $summa[$i][$numberOfStages + 11] = $positions[$row[3]];
}
$summaFileData = [];
$headLine = '№;ФИО;ЛЕТ;ПОЛ;ГР;КОМАНДА;ГОРОД;';
for ($i = 0; $i < $numberOfStages; $i++) {
    $headLine .= $i + 1 . ';';
}
$headLine .= 'Этапы;Сумма;М;М ГР' . PHP_EOL;
$summaFileData = [];
$summaFileData[] = $headLine;
$countMembers = count($summa);
$i = 0;
foreach ($summa as $row) {
    $i++;
    array_unshift($row, $i);
    $summaFileData[] = implode(';', $row) . PHP_EOL;
}
file_put_contents($fileNameSumma, $summaFileData);

echo PHP_EOL;
