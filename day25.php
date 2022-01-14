<?php
$example1 = [
    'v...>>.vv>',
    '.vv>>.vv..',
    '>>.>v>...v',
    '>>v>>.>.v.',
    'v>v.vv.v..',
    '>.>>..v...',
    '.vv..>.>v.',
    'v.v..>>v.v',
    '....v..v.>',
];
$puzzle1 = array_map('trim', file('day25.txt'));

function move(array $map, $thing, $dx, $dy): array {
    $moving = [];
    for ($y1=0, $y2=$dy; $y1 < count($map);     $y1++, $y2 = ($y1+$dy) % count($map))
    for ($x1=0, $x2=$dx; $x1 < strlen($map[0]); $x1++, $x2 = ($x1+$dx) % strlen($map[0])) 
        if (($map[$y1][$x1] == $thing) and ($map[$y2][$x2] == '.')) $moving[] = [$y1, $x1, $y2, $x2];
    foreach ($moving as $move) {
        $map[$move[0]][$move[1]] = '.';
        $map[$move[2]][$move[3]] = $thing;
    }  
    return $map;
}

$map = $puzzle1;
for ($count=1; ; $count++) { 
    $new_map = move(move($map, '>',1,0), 'v',0,1);
    if ($map == $new_map)
        break;
    $map = $new_map;
}
print($count);