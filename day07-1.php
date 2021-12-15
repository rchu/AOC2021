<?php

$crabs = [16,1,2,0,4,2,7,1,2,14];
$crabs = explode(',', trim(file('day07.txt')[0]));

function fuel($pos) {
    global $crabs;
    return array_sum(array_map(
        function($value) use($pos) {

            return abs($value - $pos);
        },
        $crabs,
    ));
}

/* brute force 

$time_start = microtime(true);

$min_fuel = null;
$min_pos = null;
$last = 999999999999999;
for ($pos=min($crabs); $pos <= max($crabs) ; $pos++) { 
    $fuel = fuel($pos);
    if (is_null($min_fuel) or $min_fuel > $fuel) {
        $min_fuel = $fuel;
        $min_pos  = $pos;
        // print('-> ');
    }
    else
        // print('   ');

    // printf("%s %3s: %s\n", $last > $fuel ? '+ ' : '-', $pos, $fuel);
    $last = $fuel;
}
printf("Minimum fuel %d if all move to position %d (avg=%f)\n", $min_fuel, $min_pos, array_sum($crabs)/count($crabs));

printf("%f secs\n", microtime(true) - $time_start);
/* */

/* from average up or down */
$time_start = microtime(true);

$avg = floor(array_sum($crabs)/count($crabs));
$fuel_avg = fuel($avg);
$fuel_avg_up = fuel($avg + 10);
printf("Average %d fuel=%d, average+1=%d\n", $avg, $fuel_avg, $fuel_avg_up);

$min_fuel = $fuel_avg;
$min_pos  = $avg;
if ($fuel_avg < $fuel_avg_up)
    for ($pos=$avg-1; $pos >= min($crabs); $pos--) { 
        $fuel = fuel($pos);
        if ($min_fuel > $fuel) {
            $min_fuel = $fuel;
            $min_pos  = $pos;
        }
        else
            break;
        printf("- %3s: %s\n", $pos, $fuel);

    }
else
    for ($pos=$avg+1; $pos <= max($crabs); $pos++) { 
        $fuel = fuel($pos);
        if ($min_fuel > $fuel) {
            $min_fuel = $fuel;
            $min_pos  = $pos;
        }
        else   
            break;
        printf("+ %3s: %s\n", $pos, $fuel);
    }
printf("Minimum fuel %d if all move to position %d\n", $min_fuel, $min_pos);

printf("%f secs\n", microtime(true) - $time_start);
    
?>