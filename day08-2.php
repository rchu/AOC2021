<?php

use function PHPSTORM_META\map;

$input = [
    'be cfbegad cbdgef fgaecd cgeb fdcge agebfd fecdb fabcd edb | fdgacbe cefdb cefbgd gcbe',
    'edbfga begcd cbg gc gcadebf fbgde acbgfd abcde gfcbed gfec | fcgedb cgb dgebacf gc',
    'fgaebd cg bdaec gdafb agbcfd gdcbef bgcad gfac gcb cdgabef | cg cg fdcagb cbg',
    'fbegcd cbd adcefb dageb afcb bc aefdc ecdab fgdeca fcdbega | efabcd cedba gadfec cb',
    'aecbfdg fbg gf bafeg dbefa fcge gcbea fcaegb dgceab fcbdga | gecf egdcabf bgf bfgea',
    'fgeab ca afcebg bdacfeg cfaedg gcfdb baec bfadeg bafgc acf | gebdcfa ecba ca fadegcb',
    'dbcfg fgd bdegcaf fgec aegbdf ecdfab fbedc dacgb gdcebf gf | cefg dcbef fcge gbcadfe',
    'bdfegc cbegaf gecbf dfcage bdacg ed bedf ced adcbefg gebcd | ed bcgafe cdgba cbgef',
    'egadfb cdbfeg cegd fecab cgb gbdefca cg fgcdab egfdb bfceg | gbdfcae bgc cg cgb',
    'gcafb gcf dcaebfg ecagb gf abcdeg gaef cafbge fdbac fegbdc | fgae cfgab fg bagce',
];
// $input = ['acedgfb cdfbe gcdfa fbcad dab cefabd cdfgeb eafb cagedb ab | cdfeb fcadb cdfeb cdbaf'];
$input = file('day08.txt');

$results = 0;
foreach ($input as $line) {
    $line = explode(' | ', trim($line));

    $_5 = [];
    $_6 = [];
    foreach (explode(' ', $line[0]) as $digit) {
        switch (strlen($digit)) {
            case 2: $cf = $digit; break;
            case 4: $bd = $digit; break;
            case 5: $_5[] = $digit; break;
            case 6: $_6[] = $digit; break;
        }
    }
    $bd = str_replace([$cf[0], $cf[1]], ['',''], $bd);
    
    $digits = [];
    foreach ($_5 as $digit) {
        if (str_contains($digit, $cf[0]) and str_contains($digit, $cf[1]))
            $digits[3] = str_split($digit);
        elseif (str_contains($digit, $bd[0]) and str_contains($digit, $bd[1]))
            $digits[5] = str_split($digit);
        else
            $digits[2] = str_split($digit);
    }
    foreach ($_6 as $digit) {
        if (!(str_contains($digit, $cf[0]) and str_contains($digit, $cf[1])))
            $digits[6] = str_split($digit);
        elseif (str_contains($digit, $bd[0]) and str_contains($digit, $bd[1]))
            $digits[9] = str_split($digit);
        else
            $digits[0] = str_split($digit);
    }

    $result = '';
    foreach (explode(' ', $line[1]) as $digit) {
        switch (strlen($digit)) {
            case 2: $result .= '1'; break;
            case 3: $result .= '7'; break;
            case 4: $result .= '4'; break;
            case 7: $result .= '8'; break;
            case 5:
                foreach ([2,3,5] as $i) {
                    if (5 == array_sum(array_map(
                        function($val)use($digit) {
                            return str_contains($digit,$val) ? 1 : 0;
                        },
                        $digits[$i]
                    ))) {
                        $result .= strval($i);
                        break;
                    }
                }
            break;
            case 6:
                foreach ([0,6,9] as $i) {
                    if (6 == array_sum(array_map(
                        function($val)use($digit) {
                            return str_contains($digit,$val) ? 1 : 0;
                        },
                        $digits[$i]
                    ))) {
                        $result .= strval($i);
                        break;
                    }

                }
            break;
        }        
    }
    print "$result\n";
    $results += intval($result);

}
print($results);

/*
     aaaa 
    b    c
    b    c
     dddd 
    e    f
    e    f
     gggg 

seg  num     what
2    1       cf
3    7       acf
4    4       bcdf
5    2, 3, 5 acdeg acdfg abdfg
6    0, 6, 9 ≠d ≠c ≠e
7    8

= logic =

1,7,4,8 bekend

4 - 1 = bcdf - cf = bd

b|d -> 5 else 2,3
c|f -> 3 else 2,5

b&d -> 6,9 else 0
c&f -> 0,9 else 6

 */

?>