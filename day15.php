<?php

const five = 5;
function five_times_larger($map)
{
    function plus($line, $amount) {
        $res = '';
        foreach(str_split($line) as $char)
            $res .= strval((($char-1+$amount) % 9)+1);
        return $res;
    }
    
    $max_y = count($map);
    $result = [];
    for ($y=0; $y < five; $y++)
        for ($i=0; $i < $max_y; $i++) {
            $result[$y * $max_y + $i] = '';
            for ($x=0; $x < five; $x++)
                $result[$y * $max_y + $i] .= plus($map[$i], $x+$y);
        }
    return $result;
}

function getNeighbors($node) {
    global $goal;
    $result = [];
    if ($node[0] > 0) $result[] = [$node[0]-1, $node[1]];
    if ($node[1] > 0) $result[] = [$node[0],   $node[1]-1];
    if ($node[0] < $goal[0]) $result[] = [$node[0]+1, $node[1]];
    if ($node[1] < $goal[1]) $result[] = [$node[0],   $node[1]+1];
    return $result;
}

function h($x, $y=null) {
    global $goal;

    if (is_null($y))
        return $goal[0]- $x[0] + $goal[1]-$x[1];
    else
        return $goal[0]- $x + $goal[1]-$y;
}

function get($arr, $xy, $default = null){
    return $arr[$xy[0]][$xy[1]] ?? $default;
}
function set(&$arr, $xy, $val) {
    $arr[$xy[0]][$xy[1]] = $val;
}
$map = [
    '1163751742',
    '1381373672',
    '2136511328',
    '3694931569',
    '7463417111',
    '1319128137',
    '1359912421',
    '3125421639',
    '1293138521',
    '2311944581',
];
// $map = file('day15.txt');
foreach ($map as &$line) $line = trim($line);
$map = five_times_larger($map);


$goal = [count($map)-1, strlen($map[0])-1];
$path = [[0,0]];
$cost = $map[0][0];

$openSet = [[0,[0,0]]];
$cameFrom = [];
$actualCost = [[0]];
$estimatedCost  = [[h(0,0)]];

while (count($openSet) > 0) {
    $current = array_shift($openSet)[1];
    if ($current == $goal) {
        print(get($actualCost, $current)."\n"); 
        break;
    }

    foreach(getNeighbors($current) as $neighbor) {
        $tentative_cost = get($actualCost, $current) + get($map, $neighbor);
        if ($tentative_cost >= get($actualCost, $neighbor, INF)) continue;
        set($actualCost, $neighbor, $tentative_cost);
        set($estimatedCost, $neighbor, $tentative_cost + h($neighbor));
        set($cameFrom, $neighbor, $current);
        if (!in_array($neighbor, $openSet))
            $openSet[] = [$tentative_cost + h($neighbor), $neighbor];
            usort($openSet, function($a, $b) {
                return $a[0] <=> $b[0];
            });
    }
}
?>