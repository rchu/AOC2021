<?php
require "day19.inc.php";
const input_file = 'day19.txt';
# Beacons are just [x,y,z]

function addBeacon(array $beacon1, array $beacon2): array {
    return array_map(fn($x1, $x2) => $x1 + $x2, $beacon1, $beacon2);
}

function substractBeacon(array $beacon1, array $beacon2): array {
    return array_map(fn($x1, $x2) => $x1 - $x2, $beacon1, $beacon2);
}
function intersectBeacons(array $beacon1, array $beacon2): array {
    return array_uintersect($beacon1, $beacon2, function($a, $b) {
        if ($a[0] == $b[0])
            if ($a[1] == $b[1])
                if ($a[2] == $b[2])
                    return 0;
                else return $a[2] <=> $b[2];
            else return $a[1] <=> $b[1];
        else return $a[0] <=> $b[0];
    });
}
function sortBeacons(array &$beacons): void {
    usort($beacons, function($a, $b) {
        if ($a[0] == $b[0])
            if ($a[1] == $b[1])
                if ($a[2] == $b[2])
                    return 0;
                else return $a[2] <=> $b[2];
            else return $a[1] <=> $b[1];
        else return $a[0] <=> $b[0];
    });
}
function printBeacon($beacon, $offset = "") {
    printf("{$offset}[%4d, %4d, %4d]", ...$beacon);    
}
function printBeacons($beacons, $offset = "", $tab = "  ") {
    print("{$offset}[\n");
    foreach ($beacons as $b)
        printf("$offset{$tab}[%4d, %4d, %4d],\n", ...$b);
    print("{$offset}]\n");
    }

class Scanner {
    public array $rotation = [0,0,0];
    public array $position = [0,0,0];

    private array $beacons_org = [];
    private array $beacons_rot = [];
    public array $beacons = []; 
    
    public function __construct(public int $id, array $beacons) {
        $this->beacons = $beacons;
        $this->beacons_org = $beacons;
        $this->beacons_rot = $beacons;
    }

    public static function fromLines(array $lines) {
        $id = intval(trim(array_shift($lines),'\t\n\r\0\x0B- scanner'));
    
        foreach ($lines as &$line)
            $line = array_map('intval', explode(',', trim($line)));
        return new static($id, $lines);
    }

    public function rotate($rX, $rY, $rZ) {
        if ($this->rotation != [$rX, $rY, $rZ]) {
            $this->rotation =  [$rX, $rY, $rZ];
            $this->position = [0,0,0];
            $this->beacons_rot = $this->beacons_org;

            $r = rotateVector[$rX][$rY][$rZ];
            foreach ($this->beacons_rot as &$beacon) {
                $beacon = [
                    $r[0][0] * $beacon[$r[0][1]],
                    $r[1][0] * $beacon[$r[1][1]],
                    $r[2][0] * $beacon[$r[2][1]],
                ];
            }
            $this->beacons = $this->beacons_rot;
        }
        return $this->beacons;
    }

    public function shift($dX, $dY, $dZ) {
        if ($this->position != [$dX, $dY, $dZ]) {
            $this->beacons = $this->beacons_rot;
            if ([0,0,0] != ($this->position = [$dX, $dY, $dZ]))
                foreach ($this->beacons as &$beacon)
                    $beacon = addBeacon($beacon, [$dX, $dY, $dZ]);
        }
        return $this->beacons;
    }

    public function findOverlap(Scanner $other, bool $moveAndRotate = true) {
        if (!file_exists(input_file.'.cache') or (false === ($cache = file_get_contents(input_file.'.cache'))))
            $cache = [];
        else
            $cache = unserialize($cache);

        if (isset($cache[serialize([$this->id, $other->id])])) {
            $best = $cache[serialize([$this->id, $other->id])];
            printf("findOverlap #%-2d & #%-2d [     --- cached ---     ", $this->id, $other->id);
            
        }

        else {
            $best = [1,[],[]];
            printf("                      [                        ]\rfindOverlap #%-2d & #%-2d [", $this->id, $other->id);
            foreach (rotations as $rotation) {
                
                $this->rotate(...$rotation);
                foreach ($other->beacons as $otherBeacon) {
                    $this->shift(0,0,0);
                    foreach ($this->beacons as $beacon) {
                        // if ($this->rotation == [0,3,3]){
                        //     if ($otherBeacon == [-485, -357, 347]) {
                        //         print("break\n");

                        //     }
                        // }

                        $this->shift(...substractBeacon($otherBeacon, $beacon));
                        // if ($this->position == [1105,-1205,1229]) print("cortect shift 3");
                        // if ($this->position == [-92,-2380,-20]) print("cortect shift 4");
                        $intersect = intersectBeacons($this->beacons, $other->beacons);
                        if (count($intersect) > $best[0])
                            $best = [count($intersect), $rotation, $this->position];
                    } 
                }
                print(".");  
            } 
            $cache[serialize([$this->id, $other->id])] = $best;
            file_put_contents(input_file.'.cache', serialize($cache));
        }
        printf("] %d%s\n", $best[0], $best[0] >= 12 ? " >= 12":"");
        if ($best[0] > 1) {
            if ($moveAndRotate) {
                $this->rotate(...$best[1]);
                $this->shift(...$best[2]);
            }
            return $best;
        }
        else
            return null;
    }
}

class Scanners {
    public array $scanners = [];
    public array $beacons = [];

    public function add(Scanner $scanner) {
        $this->scanners[$scanner->id] = $scanner;    
        if ($scanner->id == 0)
            $this->beacons = $scanner->beacons;
    }
    public function findOverlap() {

        $found = [0];
        $search = range(1, count($this->scanners)-1);
        // $search = [1,4];
        while ($found) {
            $fnd = array_shift($found);
            foreach ($search as $srch)
             if (
                 !is_null($result = $this->scanners[$srch]->findOverlap($this->scanners[$fnd]))
                 and ($result[0] >= 12)) {
                unset($search[array_search($srch, $search)]);
                $found[] = $srch;
                // print_r($result);
                // break;
            }
        }
            if ($search) return false;
        $this->beacons = array_merge(...array_map(fn($a) => $a->beacons, $this->scanners));
        var_dump(count($this->beacons));
        $this->beacons = array_unique($this->beacons, SORT_REGULAR);
        var_dump(count($this->beacons));
        
    }
    public function oceanSize() {
        $c=strlen(strval(count($this->beacons)));
        $max=0;
        foreach ($this->scanners as $scanner1)
            foreach($this->scanners as $scanner2) {
                $d = (
                    abs($scanner1->position[0] - $scanner2->position[0]) +
                    abs($scanner1->position[1] - $scanner2->position[1]) +
                    abs($scanner1->position[2] - $scanner2->position[2])
                );
                if ($d > $max) $max = $d;
            }
            print("Max distance is $max\n");

        // sortBeacons($this->beacons);
        // printBeacons($this->beacons);
        // return;


        for ($i=0; $i < count($this->beacons); $i++) {
            for ($j=0; $j < count($this->beacons); $j++) { 
                // printBeacon($this->beacons[array_keys($this->beacons)[$i]]);
                // print(" & ");
                // printBeacon($this->beacons[array_keys($this->beacons)[$j]]);
                printf( "%{$c}d, %{$c}d %d       ", $i, $j, $d = (
                    
                    abs($this->beacons[array_keys($this->beacons)[$i]][0] - $this->beacons[array_keys($this->beacons)[$j]][0]) + 
                    abs($this->beacons[array_keys($this->beacons)[$i]][1] - $this->beacons[array_keys($this->beacons)[$j]][1]) + 
                    abs($this->beacons[array_keys($this->beacons)[$i]][2] - $this->beacons[array_keys($this->beacons)[$j]][2]) 
                ));
                if ($d > $max) {
                    print(" ***\n");
                    $max = $d;
                }
                else
                    print("                \r");
            }     
        }
    }
}

$lines = [];
$scanners = new Scanners();
foreach (file(input_file) as $line)
    if (trim($line) == '') {
        $scanners->add(Scanner::fromLines($lines));
        $lines = [];
    }
    else 
        $lines[] = $line;
$scanners->add(Scanner::fromLines($lines));
$scanners->findOverlap();
$scanners->oceanSize();


?>