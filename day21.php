<?php
$position = [4,8]; # example
$position = [2,5]; #puzzle input;
$score = [0, 0];


$dieRolls = 0;
function dieRoll() {
    global $dieRolls;
    static $value = 0;
    $dieRolls += 3;
    return ($value++ % 100) + ($value++ % 100) + ($value++ % 100) + 3;
}

$player = 0;
while (true) {
    $position[$player] = ($position[$player] + dieRoll() - 1) % 10 + 1;
    $score[$player] += $position[$player];
    printf("Player %d score = %d\n", $player, $score[$player]);
    if ($score[$player] >= 1000) {
        printf("Player %d wins!\n", $player);
        break;
    }
    $player = ($player + 1) % 2;
}
printf('Losing player %1$d has score %2$d, %3$d die rolls, %2$d * %3$d = %4$d'."\n",
    1-$player,
    $score[1-$player],
    $dieRolls,
    $score[1-$player] * $dieRolls,
);


function quantumDieRoll($position, $score, $turn = 0, $depth = 0) {
    static $cache = [];
    $result = $cache[serialize(func_get_args())] ?? false;

    if (!$result) foreach ([0,1] as $player)
        if ($score[$player] >= 21)
            $result = [1-$player, $player];

    if (!$result) {
        $result = [0,0];
        foreach ([3, 4, 5, 4, 5, 6, 5, 6, 7, 4, 5, 6, 5, 6, 7, 6, 7, 8, 5, 6, 7, 6, 7, 8, 7, 8, 9] as $roll) {
            $_position = $position;
            $_position[$turn] = ($_position[$turn] + $roll - 1) % 10 + 1;
            $_score = $score;
            $_score[$turn] += $_position[$turn];
            $wins = quantumDieRoll($_position, $_score, 1 - $turn, $depth+1);
            $result = [$result[0] + $wins[0], $result[1] + $wins[1]];
        }
    }

    $cache[serialize(func_get_args())] = $result;
    return $result;
}

$position = [4,8]; # example
$position = [2,5]; #puzzle input;
$score = [0, 0];
print_r(quantumDieRoll($position, $score));
