<?php
function print_dots($dots) 
{
    $max_x = 0;
    $max_y = 0;
    foreach ($dots as $dot) {
        $max_x = max($max_x, $dot[0]);
        $max_y = max($max_y, $dot[1]);
    }
    $output = array_fill(0,$max_y+1, str_repeat('.',$max_x+1));
    foreach ($dots as $dot) {
        $output[$dot[1]][$dot[0]] = '#'; 
    }
    print(implode("\n", $output) . "\n");
}

$input = ['6,10', '0,14', '9,10', '0,3', '10,4', '4,11', '6,0', '6,12', '4,1', '0,13', '10,12', '3,4', '3,0', '8,4', '1,10', '2,14', '8,10', '9,0', '', 'fold along y=7', 'fold along x=5', ];
$input = file('day13.txt');

$dots = [];
$folds = null;
foreach ($input as $line):
    if (trim($line))
        if (is_null($folds)):
            $dots[] = explode(',', trim($line));
        else:
            $line = explode(' ', trim($line));
            $folds[] = explode('=', end($line));
        endif;
    else
        $folds = [];

endforeach;

print_dots($dots);

foreach ($folds as $fold) {
    printf("Folding at %s = %d\n", $fold[0], $fold[1]);
    if ($fold[0] == 'x') { 
        foreach ($dots as &$dot)
            if ($dot[0] > $fold[1]) $dot[0] = 2 * $fold[1] - $dot[0];
    } else {
        foreach ($dots as &$dot)
            if ($dot[1] > $fold[1]) $dot[1] = 2 * $fold[1] - $dot[1];
    }
    print_dots($dots);
    printf("There are %d of %d dots left\n\n", count(array_unique($dots, SORT_REGULAR)), count($dots));        
}
