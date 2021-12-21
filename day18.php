<?php


function addNumbers(&$input) {
    foreach ($input as &$line)
        eval('$line = '.trim($line).';');
    
    $result = array_shift($input);
    while (count($input) > 0)
        $result = [$result, array_shift($input)];

    $input = $result;
}

function addReduceNumbers(&$input) {
    foreach ($input as &$line)
        eval('$line = '.trim($line).';');
    
    $num = array_shift($input);
    while (count($input) > 0) {
        $num2 = array_shift($input);
        reduce($num);
        reduce($num2);
        $num = [$num, $num2];
    }
    reduce($num);
    $input = $num;
}

function &_explodePair(&$array, &$things, $depth) {
    foreach ($array as &$a)
        if (is_array($a)) 
            if (($depth >= 4) and is_numeric($a[0]) and is_numeric($a[1]) and is_null($things['pair']))
                $things['pair'] = &$a;
            else  
                $things = &_explodePair($a, $things, $depth+1);
        else 
            if (is_null($things['pair'])) 
                $things['left_num']  = &$a;
            elseif (is_null($things['right_num']))
                $things['right_num'] = &$a; 
        
    return $things;
}

function explodePair(&$input) {
    $things = ['left_num' => null, 'pair' => null, 'right_num' => null ];
    $things = &_explodePair($input, $things, 1);
   
    if (is_null($things['pair'])) return false;

    if (!is_null($things['left_num'])) $things['left_num'] = $things['left_num'] + $things['pair'][0];
    if (!is_null($things['right_num'])) $things['right_num'] = $things['right_num'] + $things['pair'][1];
    $things['pair'] = 0;

    return true;
}

function &_splitPair(&$array, &$number, $depth) {
    foreach ($array as &$a)
        if (is_array($a)) 
            $number = &_splitPair($a, $number, $depth+1);
        else 
            if (($a > 9) and (is_null($number)))
                $number = &$a;
        
    return $number;
}

function splitPair(&$input) {
    $number = null;
    $number = &_splitPair($input, $number, 1);
   
    if (is_null($number)) return false;

    $number = [floor($number/2), ceil($number/2)];
    return true;
}

function reduce(&$input) {
    while (explodePair($input) or splitPair($input));
}

function magnitude(&$input) {
    if (is_array($input)) {
        magnitude($input[0]);
        magnitude($input[1]); 
        $input = 3 * $input[0] + 2 * $input[1];
    }
}

$input = [
    '[[[0,[5,8]],[[1,7],[9,6]]],[[4,[1,2]],[[1,4],2]]]',
    '[[[5,[2,8]],4],[5,[[9,9],0]]]',
    '[6,[[[6,2],[5,6]],[[7,6],[4,7]]]]',
    '[[[6,[0,7]],[0,9]],[4,[9,[9,0]]]]',
    '[[[7,[6,4]],[3,[1,3]]],[[[5,5],1],9]]',
    '[[6,[[7,3],[3,2]]],[[[3,8],[5,7]],4]]',
    '[[[[5,4],[7,7]],8],[[8,3],8]]',
    '[[9,3],[[9,9],[6,[4,9]]]]',
    '[[2,[[7,7],7]],[[5,8],[[9,3],[0,2]]]]',
    '[[[[5,2],5],[8,[3,7]]],[[5,[7,5]],[4,4]]]',
];
// $input = file('day18.txt');

addReduceNumbers($input);
magnitude($input);
print("Magnitude is $input\n");

$input = [
    '[[[0,[5,8]],[[1,7],[9,6]]],[[4,[1,2]],[[1,4],2]]]',
    '[[[5,[2,8]],4],[5,[[9,9],0]]]',
    '[6,[[[6,2],[5,6]],[[7,6],[4,7]]]]',
    '[[[6,[0,7]],[0,9]],[4,[9,[9,0]]]]',
    '[[[7,[6,4]],[3,[1,3]]],[[[5,5],1],9]]',
    '[[6,[[7,3],[3,2]]],[[[3,8],[5,7]],4]]',
    '[[[[5,4],[7,7]],8],[[8,3],8]]',
    '[[9,3],[[9,9],[6,[4,9]]]]',
    '[[2,[[7,7],7]],[[5,8],[[9,3],[0,2]]]]',
    '[[[[5,2],5],[8,[3,7]]],[[5,[7,5]],[4,4]]]',
];
$input = file('day18.txt');

$maxMag = 0;
for ($i=0; $i < count($input); $i++) { 
    for ($j=0; $j < count($input); $j++) {
        if ($i == $j) continue;
        $pair = [$input[$i], $input[$j]];
        addReduceNumbers($pair);
        magnitude($pair);
        if ($maxMag < $pair) $maxMag = $pair;
    }
}
print("Max Magnitude is $maxMag\n");

/* * * TEST * * */

function test_explodePair($input) {
    eval("\$array = $input;");
    explodePair($array);
    return $array;
}
function test_splitPair($input) {
    eval("\$array = $input;");
    splitPair($array);
    return $array;
}
function test_addNumbers($input) {
    $array = explode('\n', $input);
    addNumbers($array);
    return $array;
}
function test_reduce($input) {
    eval("\$array = $input;");
    reduce($array);
    return $array;
}
function test_addReduce($input) {
    $array = explode('\n', $input);
    addReduceNumbers($array);
    return $array;
}
function test_magnitude($input) {
    eval("\$array = $input;");
    magnitude($array);
    return $array;
}


function test($func, $in, $out) {
    if ($func($in) != $out)
        print("FAIL $func($in)\n");
    else
        print("PASS $func($in)\n");
}

test('test_addNumbers', '[1,1]\n[2,2]', [[1,1],[2,2]]);
test('test_addNumbers', '[1,1]\n[2,2]\n[3,3]', [[[1,1],[2,2]],[3,3]]);
test('test_addNumbers', '[1,2]\n[[3,4],5]', [[1,2],[[3,4],5]]);
test('test_explodePair', "[[[[[9,8],1],2],3],4]",  [[[[0,9],2],3],4]);
test('test_explodePair', "[7,[6,[5,[4,[3,2]]]]]",  [7,[6,[5,[7,0]]]]);
test('test_explodePair', "[[6,[5,[4,[3,2]]]],1]",  [[6,[5,[7,0]]],3]);
test('test_explodePair', "[[3,[2,[1,[7,3]]]],[6,[5,[4,[3,2]]]]]",  [[3,[2,[8,0]]],[9,[5,[4,[3,2]]]]]);
test('test_explodePair', "[[3,[2,[8,0]]],[9,[5,[4,[3,2]]]]]",  [[3,[2,[8,0]]],[9,[5,[7,0]]]]);
test('test_splitPair', "[5,[10,1]]", [5,[[5,5],1]]);
test('test_splitPair', "[5,[11,1]]", [5,[[5,6],1]]);
test('test_splitPair', "[5,[10,13]]", [5,[[5,5],13]]);
test('test_reduce', '[[[[[4,3],4],4],[7,[[8,4],9]]],[1,1]]', [[[[0,7],4],[[7,8],[6,0]]],[8,1]]);
test('test_reduce', '[[[[[1,1],[2,2]],[3,3]],[4,4]],[5,5]]', [[[[3,0],[5,3]],[4,4]],[5,5]]);
test('test_reduce', '[[[[[[1,1],[2,2]],[3,3]],[4,4]],[5,5]],[6,6]]', [[[[5,0],[7,4]],[5,5]],[6,6]]);
test('test_addReduce', '[1,1]\n[2,2]', [[1,1],[2,2]]);
test('test_addReduce', '[1,1]\n[2,2]\n[3,3]', [[[1,1],[2,2]],[3,3]]);
test('test_addReduce', '[1,2]\n[[3,4],5]', [[1,2],[[3,4],5]]);
test('test_addReduce', '[[[0,[4,5]],[0,0]],[[[4,5],[2,6]],[9,5]]]\n[7,[[[3,7],[4,3]],[[6,3],[8,8]]]]\n[[2,[[0,8],[3,4]]],[[[6,7],1],[7,[1,6]]]]\n[[[[2,4],7],[6,[0,5]]],[[[6,8],[2,8]],[[2,1],[4,5]]]]\n[7,[5,[[3,8],[1,4]]]]\n[[2,[2,2]],[8,[8,1]]]\n[2,9]\n[1,[[[9,3],9],[[9,0],[0,7]]]]\n[[[5,[7,4]],7],1]\n[[[[4,2],2],6],[8,7]]', [[[[8,7],[7,7]],[[8,6],[7,7]]],[[[0,7],[6,6]],[8,7]]]);
test('test_magnitude', '[9,1]', 29);
test('test_magnitude', '[1,9]', 21);
test('test_magnitude', '[[9,1],[1,9]]', 129);
/** */
?>