<?php

$input = [
    '2199943210',
    '3987894921',
    '9856789892',
    '8767896789',
    '9899965678',
];
$input = file('day09.txt');

foreach ($input as &$line) {
    $line = array_map('intval', str_split(trim($line)));
}
$size_x = count($input[0]);
$size_y = count($input);


$basin_id = 1;
$basins = [];
$basins_size = [];

function basin_set($x,$y, $x2 = null, $y2 = null) {
    global $basins, $basins_size, $basin_id;
    if (is_null($x2) or is_null($y2)) {
        $basins[$y][$x] = $basin_id;
        $basins_size[$basin_id] = 1;
        $basin_id++;
    }
    else {
        $basins[$y][$x] = $basins[$y2][$x2];
        $basins_size[$basins[$y2][$x2]]++;
    }
    
}
function basin_merge($from, $into) {
    global $basins, $basins_size;
    if ($from == $into)
        return;
    foreach ($basins as &$line)
        foreach ($line as &$pos)
            if ($pos == $from) {
                $pos = $into;
                $basins_size[$from]--;
                $basins_size[$into]++;
            }
    if ($basins_size[$from] > 0)
        throw new Exception("Basin $from should be empty", 1);
    else
        unset($basins_size[$from]);
        
}

for ($y = 0; $y < $size_y; $y++) {
    $basins[$y] = [];
    for ($x = 0; $x < $size_x; $x++) {
        
        # 9 is never part of a basin
        if ($input[$y][$x] == 9) { 
            $basins[$y][$x] = 0;
            continue;
        }

        if ($x == 0) {
            # Look nowhere: top left corner
            if ($y == 0) 
                basin_set($x,$y);
            # look up 
            else 
                if ($basins[$y-1][$x] == 0)
                    basin_set($x,$y);
                else
                    basin_set($x,$y,$x,$y-1);
        }
        else { 
            # Look left
            if ($basins[$y][$x-1] == 0)
                basin_set($x,$y);
            else
                basin_set($x,$y,$x-1,$y);
            
            # Look up
            if ($y > 0 and $basins[$y-1][$x] != 0)
                basin_merge(
                    $basins[$y][$x],
                    $basins[$y-1][$x],
                );
            
        }
    }
}

# print basins
foreach ($basins as $line) {
    foreach ($line as $char) {
        if ($char == 0)
            print('  ');
        else
            printf("\e[0;31;%dm%02d",31 + $char % 7, $char % 100);
        
    }
    print("|\n");
}
asort($basins_size);
print_r($basins_size);

print_r(array_product(array_slice($basins_size,-3, preserve_keys:true)));
?>