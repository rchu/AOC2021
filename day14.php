<?php
/**
 * Returns a cached version of a function
 */
function cached(callable $func): callable {
    return function() use ($func) {
        static $cache = [];
        $args = func_get_args();
        $key = serialize($args);
        if (isset($cache[$key]))
            return $cache[$key];
        else
            return $cache[$key] = call_user_func_array($func, $args);
    };
}

// expand AB -> AXXXXXXXXXB $depth deep //
$expand_depth = 10;
$expand = cached(function($input) {
    global $expand_depth, $rules;

    $old = $input;
    for ($step=1; $step <= $expand_depth; $step++) { 
        $new = '';
        for ($i=0; $i < strlen($old)-1; $i++)
            $new .= $old[$i] . $rules[substr($old, $i,2)];
        $new .= $old[$i];
        $old = $new;
    }
    return $new;
});

// resolve input in $depth steps
$resolve = cached(function($input, $depth) {
    global $expand_depth, $resolve, $expand;
    
    if ($depth == 0):
        # count chars and return
        $result = [];
        foreach (str_split($input,1) as $char)
            $result[$char] = ($result[$char] ?? 0) + 1;

        return $result;

    else:
        # expand between all chars
        $result = [];
        for ($i=0; $i < strlen($input)-1; $i++)
            foreach ($resolve($expand(substr($input, $i, 2)), $depth - $expand_depth) as $char => $count)
                $result[$char] = ($result[$char] ?? 0) + $count;

        # remove double counted chars
        for ($j=1; $j < strlen($input)-1; $j++)
            $result[$input[$j]]--;

        return $result;
    
    endif;
});

// Input //
$input =  ['NNCB', '', 'CH -> B', 'HH -> N', 'CB -> H', 'NH -> C', 'HB -> C', 'HC -> B', 'HN -> C', 'NN -> C', 'BH -> H', 'NC -> B', 'NB -> B', 'BN -> B', 'BB -> N', 'BC -> B', 'CC -> N', 'CN -> C',];
$input = file('day14.txt');

// Parse Input //
$template = trim(array_shift($input));
array_shift($input);
$rules = [];
foreach ($input as $line) {
    $line = explode(' -> ', trim($line));
    $rules[$line[0]] = $line[1];
}
$input = $template;

// run
$res = $resolve($input,40);
asort($res);
print_r($res);
print(end($res) - array_shift($res));

?>
