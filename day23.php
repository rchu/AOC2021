<?php

// 
// Game logic: halls, rooms
//
const ROOM_COUNT = 4;
const HALL_SIZE = ROOM_COUNT + ROOM_COUNT-1 + 4;
const COST = [1, 10, 100, 1000];

function gameStrVal(int $i): string {
    return ($i == -1) ? '.': strval($i); 
}
function hall(array $game, int $position): int {
    return $game[1 + $position];
}
function room(array $game, int $room_id, int $room_position): int {
    return $game[1 + HALL_SIZE + $game[0]*$room_id + $room_position];
}
function setHall(array $game, int $position, int $amphipod): array { 
    $game[1 + $position] = $amphipod;
    return $game;
}
function setRoom(array $game, int $room_id, int $room_position, int $amphipod): array {
    $game[1 + HALL_SIZE + $game[0]*$room_id + $room_position] = $amphipod;
    return $game;
}
//
// Print game state
//
const PRINT_DELAY_MS = 0;
const NONE = 0;
const RED = 1;
const GREEN = 2;
const COLOR = [
    0 => "\033[0m",
    1 => "\033[0;31m",
    2 => "\033[0;32m",
];
function printGame(array $game, $label="", $color = NONE, $end="") {
    if (PRINT_DELAY_MS < 0) return;
    static $time_last_print = 0;
    static $count=1;

    if (!$label) $label = "(".strval($count++).")";
    $result =
        implode(array_map('gameStrVal', array_slice($game, 1, HALL_SIZE))) ."\n".
        str_repeat(str_repeat(' ', HALL_SIZE)."\n",$game[0]) .
        $label."\n";
    
    for ($room=0; $room < ROOM_COUNT; $room++) 
        for ($pos=0; $pos < $game[0]; $pos++) { 
            $result[($game[0]-$pos)*(HALL_SIZE+1) + 2 + $room*2] = gameStrVal(room($game, $room, $pos));
    }
 
    while (hrtime(true) < ($time_last_print + PRINT_DELAY_MS))
        usleep(1000);

    print(COLOR[$color].$result.COLOR[NONE]);
    $time_last_print = hrtime(true);
    if ($end)
        print($end);
    else
        print("\r".str_repeat("\033[A", 2+$game[0]));
}

/**
 * returns optimistic estimation of moving cost from given status to solution
 * 
 * eximation is sum of all enities moving the shortest route to their hall. Does not account for
 * position 1 or 2 in hall and moving out of each others ways.
 */
function heuristic(array $game): int
{
    $total_cost = 0;
    // cost of moving * (get out + move over + get in) 
    for ($room=0; $room < ROOM_COUNT; $room++) {
        for ($roomPos=0; $roomPos < $game[0]; $roomPos++) { 
            $amphipod = room($game, $room, $roomPos);
            if (($amphipod != -1) and ($amphipod != $room))
                $total_cost += COST[$amphipod] * ($game[0]-$roomPos + 2*abs($amphipod-$room) + 1);
        }
    }
    // const of moving * (move over + get in)
    for ($hall=0; $hall < HALL_SIZE; $hall++) {
        $amphipod = hall($game, $hall);
        if ($amphipod != -1)
            $total_cost += COST[$amphipod] * (abs(2 + 2*$amphipod - $hall) + 1);
    }
    return $total_cost;
}


/**
 * returns array of all possible configurations one step away from the given one an the cost of getting there
 * 
 * @return array [[cost, game], ...]
 */
function neighbours(array $game): array
{
    $results = [];

    # move from hall to room
    for ($hall=0; $hall < HALL_SIZE; $hall++) { 
        if (-1 == ($amphipod = hall($game, $hall))) continue;
        $notok = false;
        for ($_pos=0; $_pos < $game[0]; $_pos++) { 
            if (room($game, $amphipod, $_pos) == -1) break;
            if (room($game, $amphipod, $_pos) != $amphipod) $notok = true;
        }
        if ($notok) continue;
        if ($game[0] <= ($roomPos = $_pos)) continue;

        # check if possible & calc cost
        $cost = abs(2+2*$amphipod - $hall);      
        if (-$cost != array_sum(array_slice($game, 1 +min($hall+1, 2+2*$amphipod), $cost))) continue;
        $cost = COST[$amphipod] * ($cost + $game[0]-$roomPos);

        # Add move to results
        $result = setRoom($game, $amphipod, $roomPos, $amphipod);
        $result = setHall($result, $hall, -1);
        $results[] = [$cost, $result];
    }
    
    # move from room to hall 
    for ($room=0; $room < ROOM_COUNT; $room++) {
        

        for ($roomPos=$game[0]-1; $roomPos >= 0; $roomPos--)
            if (($amphipod = room($game, $room, $roomPos)) != -1) break;
        if ($roomPos < 0) continue;

        if ($amphipod == $room) {
            $wrong_amphipod_in_room = false;
            for ($i=$roomPos-1; $i >= 0; $i--) if (room($game, $room, $i) != $amphipod)
                $wrong_amphipod_in_room = true;
            if (!$wrong_amphipod_in_room) continue;
        }

        foreach ([0,1,3,5,7,9,10] as $hall) { 
            if (hall($game, $hall) != -1) continue;
                
            # check if possible & calc cost
            $cost = abs(2+2*$room - $hall);      
            if (-$cost != array_sum(array_slice($game, 1 + min($hall+1, 2+2*$room), $cost))) continue;
            $cost = COST[$amphipod] * ($cost + $game[0]-$roomPos);

            # Add to results
            $result = setHall($game, $hall, room($game, $room, $roomPos));
            $result = setRoom($result, $room, $roomPos, -1);
            $results[] = [$cost, $result];
        }
    }

    return $results;
}

function game2str(array $game): string {
    $result='';
    foreach ($game as $val) {
        $result .= chr($val+97);
    }
    return $result;
}
function game2array(string $game): array {
    $result = [];
    for ($i=0; $i < strlen($game); $i++) { 
        $result[] = ord($game[$i])-97;
    }
    return $result;
}
function solved(array $game): bool {
    static $solutions = [];

    if (isset($solutions[$game[0]]))
        return $game == $solutions[$game[0]];
    else {
        $solution = [$game[0]] + array_fill(1,HALL_SIZE,-1);
        for ($room=0; $room < ROOM_COUNT; $room++)
            $solution = array_merge($solution, array_fill(0,$game[0], $room));
        $solutions[$game[0]] = $solution;
        return $game == $solution;
    }
}
/**
 * Soles a game by A* troigh it
 */
function solve($current) {
    $currHash = game2str($current);
    $fScore[$currHash] = heuristic($current);
    $gScore[$currHash] = 0;
    $openSet[$currHash] = $fScore[$currHash];

    while ($openSet)
    {
        $best = PHP_INT_MAX;
        foreach ($openSet as $key => $val){
            if ($best <= $val) continue;
            $currHash = $key; 
            $best = $val;
        }
        unset($openSet[$currHash]);
        $currGame = game2array($currHash);

        if (solved($currGame)) {
            printGame($currGame, label: "fScore = ".intval($fScore[$currHash]), end: "\n");
            return $fScore[$currHash];
        }
        
        printGame($currGame);
        foreach (neighbours($currGame) as $neighbour) {
            list($nCost, $nGame) = $neighbour;
            $nHash = game2str($nGame);

            $gTentative = $gScore[$currHash] + $nCost;
            if (!isset($gScore[$nHash]) or ($gTentative < $gScore[$nHash])) {
                $gScore[$nHash] = $gTentative;
                $fScore[$nHash] = $gTentative + heuristic($nGame);
                $openSet[$nHash] = $fScore[$nHash];
            }
        }

    }
}

const example1 = [2, -1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,0,1,3,2,2,1,0,3]; #12521
const example2 = [4, -1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,0,3,3,1,3,1,2,2,2,0,1,1,0,2,0,3]; #44169
const puzzle1  = [2, -1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,2,3,0,1,3,0,1,2]; #15538
const puzzle2  = [4, -1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,2,3,3,3,0,1,2,1,3,0,1,0,1,2,0,2]; #47258


solve(puzzle1);
solve(puzzle2);
