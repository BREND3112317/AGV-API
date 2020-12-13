<?php

set_time_limit(1);
$_map = [    [-1,-2, 0, 0,-1,-1,-1],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-1],
            [-1, 0, 0, 0, 0, 0,-1],
];


class point{
    public $x, $y, $yaw;
    public $cost;
    public $parent;
    public function __construct($x, $y, $yaw, $cost = PHP_INT_MAX-1){
        $this->x    = $x;
        $this->y    = $y;
        $this->yaw  = $yaw;
        $this->cost = $cost;
    }
}

// class AGV extends point{
//     public function CalculationCost(){

//     }

    
// }

class DFS{

    public $map = [ [-1,-2, 0, 0,-1,-1,-1],
                    [-1,-1, 0, 0, 0,-2,-2],
                    [-1,-1, 0, 0, 0,-2,-2],
                    [-1,-1, 0, 0, 0, 0,-2],
                    [-1, 0, 0, 0, 0, 0,-2],
                    [-1, 0, 0, 0, 0, 0,-1],
                    [-1, 0, 0, 0, 0, 0,-1],
    ];

    public $map_index = array();

    public $best_path = array();
    public $best_coin = PHP_INT_MAX;

    // public $map = [ [-1,-1,-1,-1,-1,-1,-1,-1,-1],
    //                 [-1,-1,-2, 0, 0,-1,-1,-1,-1],
    //                 [-1,-1,-1, 0, 0, 0,-2,-2,-1],
    //                 [-1,-1,-1, 0, 0, 0,-2,-2,-1],
    //                 [-1,-1,-1, 0, 0, 0, 0,-2,-1],
    //                 [-1,-1, 0, 0, 0, 0, 0,-2,-1],
    //                 [-1,-1, 0, 0, 0, 0, 0,-1,-1],
    //                 [-1,-1, 0, 0, 0, 0, 0,-1,-1],
    //                 [-1,-1,-1,-1,-1,-1,-1,-1,-1],
    // ];

    public $start, $end;

    public function __construct($_map = null){
        if($_map!==null){
            $this->map = $_map;
        }
    }

    public function YawCost($x, $y, $p){ // 節點道起點的移動代價，對應上文的g(n)
        $cost = 1;
        $cx = $p->x-$x;
        $cy = $p->y-$y;
        if($cx==-1)$cost += 2;
        else if($cy !=0 )$cost += 1;
        return $cost;
    }

    public function IsValidPoint($x, $y){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
        if($x < 0 || $y < 0)return false;
        if($x >= count($this->map) || $y >= count($this->map[0]))return false;
        //echo "(" . $x . ", " . "): " . $this->map[$x][$y] . '<br/>';
        return $this->map[$y][$x] === 0;
    }

    public function setStartPoint($p){ // 設定起點
        $this->start = new Point($p->x, $p->y, $p->yaw, 0);
    }

    public function IsStartPoint($p){ // 判斷點是否為起點
        return $this->start->x == $p->x && $this->start->y == $p->y;
    }

    public function setEndPoint($p){ // 設定終點
        $this->end = new Point($p->x, $p->y, $p->yaw);
    }

    public function IsEndPoint($p){ // 判斷點是否為終點
        return $this->end->x == $p->x && $this->end->y == $p->y;
    }

    public function Run($start_p, $end_p){
        $this->setStartPoint($start_p);
        $this->setEndPoint($end_p);

        $this->ProcessPoint($start_p->x,    $start_p->y+1,  $start_p);
        $this->ProcessPoint($start_p->x-1,  $start_p->y,    $start_p);
        $this->ProcessPoint($start_p->x,    $start_p->y-1,  $start_p);
        $this->ProcessPoint($start_p->x+1,  $start_p->y,    $start_p);
    }

    public function ProcessPoint($x, $y, $parent){
        $p = new point($x, $y, $parent->cost+1);
        if($this->IsValidPoint($p->x, $p->y)==false) return;
        if($this->IsEndPoint($p)){
            $this->BuildPath($p);
            return;
        }
        if(isset($this->map_index[$y][$x])==false || $this->map_index[$y][$x]->cost > $p->cost){
            $this->map_index[$y][$x] = $p;
            $this->ProcessPoint($p->x,      $p->y+1,    $p);
            $this->ProcessPoint($p->x-1,    $p->y,      $p);
            $this->ProcessPoint($p->x,      $p->y-1,    $p);
            $this->ProcessPoint($p->x+1,    $p->y,      $p);
        }
        
        //$map[$y][$x] = $this->YawCost($x, $y, $parent);
    }

    public function BuildPath($p){
        $path = array();
        $coin = 0;
        while($p!=null){
            array_push($path, $p);
            $coin += $p->coin;
            $p = $p->parent;
        }
        if($coin < $this->best_coin){
            $this->best_coin = $coin;
            $this->best_path = $path;
        }
    }
}

$bfs = new DFS();
$bfs->Run(new point(4, 2), new point(5, 5));