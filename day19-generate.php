<?php
print("<?php\n\n");

$vector = [1,2,3];
print("const rotateVector = [\n");
for ($rX=0; $rX < 4; $rX++) {
    print("    [ # x=$rX\n");
    for ($rY=0; $rY < 4; $rY++) { 
        print("        [ #y=$rY\n");
        for ($rZ=0; $rZ < 4; $rZ++) {
            $v = $vector;

            if     ($rX == 1) $v = [ $v[0],  $v[2], -$v[1]];
            elseif ($rX == 2) $v = [ $v[0], -$v[1], -$v[2]];
            elseif ($rX == 3) $v = [ $v[0], -$v[2],  $v[1]];
            
                if ($rY == 1) $v = [ $v[2],  $v[1], -$v[0]];
            elseif ($rY == 2) $v = [-$v[0],  $v[1], -$v[2]];
            elseif ($rY == 3) $v = [-$v[2],  $v[1],  $v[0]];

                if ($rZ == 1) $v = [ $v[1], -$v[0],  $v[2]];
            elseif ($rZ == 2) $v = [-$v[0], -$v[1],  $v[2]];
            elseif ($rZ == 3) $v = [-$v[1],  $v[0],  $v[2]];

            printf(
                "            [[%s, %d], [%s, %d], [%s, %d]], #z=$rZ\n",
                ($v[0] < 0)?"-1":" 1", abs($v[0])-1,
                ($v[1] < 0)?"-1":" 1", abs($v[1])-1,
                ($v[2] < 0)?"-1":" 1", abs($v[2])-1
            );

        }
        print("        ],\n");
    } 
    print("    ],\n");
}
print("];\nconst rotations = [\n");

$vector = [1,2,3];
$rotateVectors = [];
$rotations = [];
$labels = [];
for ($rX=0; $rX < 4; $rX++) {
    for ($rY=0; $rY < 4; $rY++) { 
        for ($rZ=0; $rZ < 4; $rZ++) {
            $v = $vector;
            if     ($rX == 1) $v = [ $v[0],  $v[2], -$v[1]];
            elseif ($rX == 2) $v = [ $v[0], -$v[1], -$v[2]];
            elseif ($rX == 3) $v = [ $v[0], -$v[2],  $v[1]];
            
                if ($rY == 1) $v = [ $v[2],  $v[1], -$v[0]];
            elseif ($rY == 2) $v = [-$v[0],  $v[1], -$v[2]];
            elseif ($rY == 3) $v = [-$v[2],  $v[1],  $v[0]];

                if ($rZ == 1) $v = [ $v[1], -$v[0],  $v[2]];
            elseif ($rZ == 2) $v = [-$v[0], -$v[1],  $v[2]];
            elseif ($rZ == 3) $v = [-$v[1],  $v[0],  $v[2]];



            if (in_array($v, $rotateVectors)) {
                $labels[serialize($v)] .= sprintf(", [%d, %d, %d]", $rX, $rY, $rZ);  
                continue;
            }
            $labels[serialize($v)] = sprintf("[%d, %d, %d]", $rX, $rY, $rZ);  
            $rotateVectors[] = $v;
            print("    [$rX, $rY, $rZ],\n");
        }
    } 
}
print("];\nconst rotateVectors = [\n");
    
foreach ($rotateVectors as $v) printf(
    "    [[%s, %d], [%s, %d], [%s, %d]],    # %s\n",
    ($v[0] < 0)?"-1":" 1", abs($v[0])-1,
    ($v[1] < 0)?"-1":" 1", abs($v[1])-1,
    ($v[2] < 0)?"-1":" 1", abs($v[2])-1,
    $labels[serialize($v)],
);
print("];\n");