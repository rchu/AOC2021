<?php

$input = [
    '5483143223',
    '2745854711',
    '5264556173',
    '6141336146',
    '6357385478',
    '4167524645',
    '2176841721',
    '6882881134',
    '4846848554',
    '5283751526',
];
$input = array_map('trim',file('day11.txt'));
foreach ($input as &$line) {
    $line = array_map('intval', str_split($line));
}
$max_x = count($input);
$max_y = count($input[0]);

$total_flash_count = 0;
for ($step=1; $step <= 100000; $step++) { 

    $flash = [];
    $flash_count = 0;

    # Increase all
    for ($x=0; $x < $max_x; $x++) {
        for ($y=0; $y < $max_y; $y++) { 
            $input[$x][$y] += 1;
            if ($input[$x][$y] > 9) {
                $flash[] = [$x,$y];
            }
        }
    }

    # Process all flashes
    while (count($flash) > 0) {

        
        // for ($x=0; $x < $max_x; $x++) {
        //     for ($y=0; $y < $max_y; $y++) { 
        //         if ($input[$x][$y] == 0)
        //             print("\e[0;32m*\e[0m");
        //         elseif ($input[$x][$y] > 9)
        //             print("\e[0;31m*\e[0m");
        //         else
        //             print($input[$x][$y]);
        //     }
        //     print("\n");
        // }
        // printf("interim $step: %d flashes\n", count($flash));

        $flash_count += count($flash);

        $new_flash = [];
        foreach ($flash as $xy) {
            # skip if already flashed
            if ($input[$xy[0]][$xy[1]] == 0) continue;

            for ($x=max($xy[0]-1, 0); $x < min($xy[0]+2, $max_x); $x++) 
            for ($y=max($xy[1]-1, 0); $y < min($xy[1]+2, $max_y); $y++) {
                # increase all neighhbours, set self to 0
                if ($xy == [$x, $y])
                    $input[$x][$y] = 0;
                else
                    if ($input[$x][$y] > 0) $input[$x][$y] += 1;

                # add new flashes
                if ($input[$x][$y] == 10) {
                    $new_flash[] = [$x,$y];
                }
            }
        }
        $flash = array_unique($new_flash, SORT_REGULAR);
    }

    $total_flash_count += $flash_count;
    print("After step $step: $flash_count flashes ($total_flash_count total)\n");
    for ($x=0; $x < $max_x; $x++) {
        for ($y=0; $y < $max_y; $y++) { 
            if ($input[$x][$y] == 0)
                print("\e[0;33m0\e[0m");
            else
                print($input[$x][$y]);
        }
        print("\n");
    }
    if ($flash_count == $max_x * $max_y) {
        print('They all flash uniformly!!!');
        break;
    }
}




?>