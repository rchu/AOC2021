<?php
const x = 0;
const y = 1;
const dx = 2;
const quit_after_n_misses = 100000;

$target = [[20,30], [-5,-10]]; # example
$target = [[119, 176], [-84, -141]]; # puzzle input

function probeTrajectory($x, $y) {
    $posX = 0;
    $posY = 0;
    for ($i=0;; $i++) { 
        $posX += $x;
        $posY += $y;
        yield [$posX, $posY, $x, $y];
        $x -= ($x <=> 0);
        $y--;
    }
}

function all_shots() {
    for ($r=0;; $r++)
        for ($x=0; $x <= $r ; $x++) { 
            $y = $r - $x;
            yield [$x, $y];
            if ($y != 0) yield [$x, -$y];
        }
}

$absMaxY = 0;
$hits = 0;
$miss = 0;
foreach (all_shots() as list($x, $y)) {
    if ($miss > quit_after_n_misses) break;

    $maxY = 0;
    foreach (probeTrajectory($x, $y) as $step) {
        if ($step[y] > $maxY) $maxY = $step[y];

        
        if ($step[x] > $target[x][1]) {
            print("($x, $y) overshot X: {$step[x]} > {$target[x][1]}  \r");
            $miss++;
            break;
        }
        elseif ($step[x] < $target[x][0] and $step[dx] == 0)  {
            print("($x, $y) Undershot X: {$step[x]} < {$target[x][0]}  \r");
            $miss++;
            break;
        }

        if ($step[y] < $target[y][1]) {
            print("($x, $y) overshot Y: {$step[y]} < {$target[y][1]}  \r");
            $miss++;
            break;
        }

        if ($step[x] >= $target[x][0])
            if ($step[y] <= $target[y][0]) {
                printf("HIT %d ($x, $y, $maxY) misses=$miss", ++$hits);
                $miss = 0;
                if ($maxY > $absMaxY) {
                    $absMaxY = $maxY;
                    print(" AbsMaxY = $maxY");
                }
                print("                                        \n");
                break;
            }
    }
}
print("\n\nAbsMaxY = $absMaxY\n");

?>