<?php

class CaveSystem
{
    public array $items = [];
    public ?Cave $start;
    public ?Cave $end;

    /**
     * Adds a cave to $this->items, returns true on success en false on failure.
     * @return bool True if item is added. Will not add when duplicate cave/name or start/end when one is already set
     */
    public function add(Cave $cave): bool
    {
        if (in_array($cave, $this->items))
            # duplicate cave
            return false;
        if (isset($this->items[$cave->name]))
            # duplicate name
            return false;
        else
            if ($cave->start and isset($this->start))
                # duplicate start
                return false;
            elseif ($cave->end and isset($this->end))
                # duplicate end
                return false;
            else {
                $this->items[$cave->name] = $cave;
                if ($cave->start) $this->start = $cave;
                if ($cave->end)   $this->end   = $cave;
                return true;
            }
    }

    public function isset(string $name): bool
    {
        return isset($this->items[$name]);
    }


    public function print(): void 
    {
        if (count($this->items) == 0) return;
        
        $rest = $this->items;
        $next = ['start'];
        while (count($rest)) {
            if (count($next)) {
                $name = array_shift($next);
                $curr = $rest[$name];
                unset($rest[$name]);
            } 
            else
                $curr = array_shift($rest);

            printf("%-5s  ", $curr->name);
            foreach ($curr->connections as $c) {
                if (isset($rest[$c->name]) and !in_array($c->name, $next)) $next[] = $c->name;
                printf(" %s", $c->name);
            }
            print("\n");
        }
    
    }
}


class Cave
{
    public bool $big;
    public bool $end;
    public bool $start;
    public array $connections = [];

    function __construct(public string $name, public ?CaveSystem $caveSystem)
    {
        if ($name == 'start') {
            $this->start = true;
            $this->end = false;
            $this->big = false;
        }
        elseif  ($name == 'end') {
            $this->start = false;
            $this->end = true;
            $this->big = false;
        }
        else {
            $this->start = false;
            $this->end = false;
            $this->big = ctype_upper($name);
        }

        if (isset($caveSystem))
            $caveSystem->add($this);
    }

    public function add_connection(Cave $cave, bool $create_reverse_connection = true): bool
    {
        if (in_array($cave, $this->connections))
            # connection exists
            return false;

        if ($this == $cave)
            # no self-connections
            return false;

        $this->connections[] = $cave;
        if ($create_reverse_connection)
            return $cave->add_connection($this, false);
        
        return true;
    }
}


/**
 * Traverses a series of $caves and returns all paths
 * 
 * @param $caves 
 * @param $start Name of start cave
 * @param $end Name of endpoint
 * $param $visited array of Caves already visited (small caves can only be visited once)
 * @param 
 */
function find_paths(CaveSystem $caves, Cave $start, Cave $end, array $visited = [], bool $visited_small_twice = false): ?array
{
    if ($start == $end)
    return [[$end->name]];
    
    if (!$start->big):
        if (in_array($start, $visited))
            if ($visited_small_twice or $start->start):
                return null;
            else:   
                $visited_small_twice = true;
            endif;
            $visited[] = $start;
    endif;       
    
    $result = [];
    foreach ($start->connections as $conn) {
        
        if (is_null($paths = find_paths($caves, $conn, $end, $visited, $visited_small_twice))) continue;
        foreach ($paths as $path) {
            if (is_null($path)) continue;
            array_unshift($path, $start->name);
            $result[] = $path;
        }
    }
    return $result;
}

$example1 = ['start-A', 'start-b', 'A-c', 'A-b', 'b-d', 'A-end', 'b-end'];
$example2 = ['dc-end', 'HN-start', 'start-kj', 'dc-start', 'dc-HN', 'LN-dc', 'HN-end', 'kj-sa', 'kj-HN', 'kj-dc'];
$example3 = ['fs-end', 'he-DX', 'fs-he', 'start-DX', 'pj-DX', 'end-zg', 'zg-sl', 'zg-pj', 'pj-he', 'RW-he', 'fs-DX', 'pj-RW', 'zg-RW', 'start-pj', 'he-WI', 'zg-he', 'pj-fs', 'start-RW'];
$input = ['mj-TZ', 'start-LY', 'TX-ez', 'uw-ez', 'ez-TZ', 'TH-vn', 'sb-uw', 'uw-LY', 'LY-mj', 'sb-TX', 'TH-end', 'end-LY', 'mj-start', 'TZ-sb', 'uw-RR', 'start-TZ', 'mj-TH', 'ez-TH', 'sb-end', 'LY-ez', 'TX-mt', 'vn-sb', 'uw-vn', 'uw-TZ'];

$input = $input;
$caves = new CaveSystem();
foreach ($input as $line) {
    $items = explode('-',trim($line));
    if (!$caves->isset($items[0])) new Cave($items[0], $caves);
    if (!$caves->isset($items[1])) new Cave($items[1], $caves);
    $caves->items[$items[0]]->add_connection($caves->items[$items[1]]);
}

$caves->print();
$i=1;
foreach (find_paths($caves, $caves->items['start'], $caves->items['end']) as $path) {
    printf("%2d: %s\n", $i++, implode(', ', $path));
}
?>