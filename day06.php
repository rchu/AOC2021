<?php

$fish = [3,4,3,1,2];
$fish   = explode(',', trim(file('day06.txt')[0]));

$population = [0,0,0,0,0,0,0,0,0];
foreach ($fish as $f) {
    $population[$f] += 1;
}

echo sprintf("%3s  %6s total, population is %4d %4d %4d %4d %4d %4d %4d %4d %4d ", 0, array_sum($population), ...$population), "\n";
for ($day=1; $day <= 256; $day++) { 

    $new = array_slice($population, 1,8);
    $new[] = $population[0];
    $new[6] += $population[0];
    $population = $new;
    unset($new);
    
    if ($day == 80 or $day == 256) {
        echo sprintf("%3s  %6s total, population is %4d %4d %4d %4d %4d %4d %4d %4d %4d ",$day, array_sum($population), ...$population), "\n";
    }
}
?>