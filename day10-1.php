<?php

$input = [
    '[({(<(())[]>[[{[]{<()<>>',
    '[(()[<>])]({[<{<<[]>>(',
    '{([(<{}[<>[]}>{[]{[(<()>',
    '(((({<>}<{<{<>}{[]{[]{}',
    '[[<[([]))<([[{}[[()]]]',
    '[{[{({}]{}}([{[{{{}}([]',
    '{<[[]]>}<{[{[{[]{()[[[]',
    '[<(<(<(<{}))><([]([]()',
    '<{([([[(<>()){}]>(<<{{',
    '<{([{{}}[<[[[<>{}]]]>[]]',
];
$input = array_map('trim',file('day10.txt'));
$input_length = max(array_map('strlen', $input));

$valid_chars = [
    '(' => ')',
    '[' => ']',
    '{' => '}',
    '<' => '>',
];
$char_error_score = [
    ')'=> 3,
    ']'=> 57,
    '}'=> 1197,
    '>'=> 25137,
];
$char_complete_score = [
    ')'=> 1,
    ']'=> 2,
    '}'=> 3,
    '>'=> 4,
];
$error_score = 0;
$complete_scores = [];
foreach ($input as $line) {
    $stack = [];
    $complete_score = 0;
    foreach (str_split($line) as $char) {
        if (isset($valid_chars[$char])) 
            array_push($stack, $valid_chars[$char]);
        
        elseif ($char == end($stack))
            array_pop($stack);
        
        else {
            printf("%-{$input_length}s Expected '%s' but found '%s' instead.\n", $line, end($stack), $char);
            $error_score += $char_error_score[$char];
            $stack = [];
            break;
        }
    }
    if (count($stack) > 0) {
        $stack = array_reverse($stack);
        foreach ($stack as $char)
            $complete_score = $complete_score * 5 + $char_complete_score[$char];
        $complete_scores[] = $complete_score;
        printf("%-{$input_length}s Completed by adding %s (%d points).\n", $line, implode($stack), $complete_score);

    }

}
print("\nError score = $error_score\n");

sort($complete_scores);
$complete_score = $complete_scores[floor(count($complete_scores)/2)];
print("Complete score = $complete_score");

?>