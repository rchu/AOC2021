<?php

// const input_file = 'day20-example.txt'; #35
// const input_file = 'day20-example2.txt'; #5326
const input_file = 'day20.txt';

$input = file(input_file);
$enhancement = trim(array_shift($input));
assert(strlen($enhancement) == 512);
array_shift($input);
$image = [];
foreach ($input as $line) $image[] = str_split(trim($line));
unset($input);

// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = // 

function printImage($image, $end="\n") {
    foreach ($image as $row)
        print(implode("", $row)."\n");
    printf(
        "%d of %d  pixels lit.\n\n\n", 
        array_sum(array_map(
            fn($x) => array_sum(array_map(
                fn($y) => $y=='#',
                $x
            )),
            $image
        )),
        count($image)*count($image[0]),
    );
}
function enhanceImage($image) {
    global $enhancement;
    static $times = 0;
    $times++;
    $infinite_space = (($times % 2 == 0) and ($enhancement[0]=='#')) ? '#' : '.';
    $result = [];
    for ($x=0; $x < 2+count($image[0]); $x++)
    for ($y=0; $y < 2+count($image); $y++) { 
        $bin = '';
        for ($dy=-1; $dy <= 1; $dy++)
        for ($dx=-1; $dx <= 1; $dx++)
            $bin .= ($image[$y+$dy-1][$x+$dx-1] ?? $infinite_space) == '#' ? '1' : '0';
        
        $dec = base_convert($bin, 2, 10);
        $result[$y][$x] = $enhancement[intval($dec)];
    }
    return $result;
}

// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = // 

printImage($image);
for ($i=1; $i <= 50; $i++) {
    print("enhancement #$i:\n");
    printImage($image = enhanceImage($image));
}
