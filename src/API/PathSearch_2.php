<?php

set_time_limit(1);

$_map = [   [-1,-2, 0, 0,-1,-1,-1],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-1],
            [-1, 0, 0, 0, 0, 0,-1],
];


class point{
    public $x, $y, $cost;
    public $parent;
    public function __construct($_x, $_y, $_cost = PHP_INT_MAX-1){
        $this->x = $_x;
        $this->y = $_y;
        $this->cost = $_cost;
    }
}

// class AGV extends point{
//     public function CalculationCost(){

//     }

    
// }

define("DEBUG", true);

class AStar{
    public $map = [ [-1,-2, 0, 0,-1,-1,-1],
                    [-1,-1, 0, 0, 0,-2,-2],
                    [-1,-1, 0, 0, 0,-2,-2],
                    [-1,-1, 0, 0, 0, 0,-2],
                    [-1, 0, 0, 0, 0, 0,-2],
                    [-1, 0, 0, 0, 0, 0,-1],
                    [-1, 0, 0, 0, 0, 0,-1],
    ];

    public $start, $end;

    public $x_dis, $y_dis;

    public $open_set = array(), $close_set = array();

    public function __construct($map){
        $this->map = $map;
    }

    public function BaseCost($p){ // 節點道起點的移動代價，對應上文的g(n)
        $this->x_dis = $p->x;
        $this->y_dis = $p->y;
        return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis);
    }

    public function HeuristicCost($p){ // 節點到終點的啟發函數，對應上文的h(n)。於是我們基於網格的圖形，所以這個函數和上一個函數用的對角距離
        $this->x_dis = count($this->map) - 1 - $p->x;
        $this->y_dis = count($this->map[0]) - 1 - $p->y;
        return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis);
    }

    public function TotalCost($p){ // 代價總和，也是上面提到的f(n)
        return $this->BaseCost($p) + $this->HeuristicCost($p);
    }

    public function IsValidPoint($x, $y){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
        if($x < 0 || $y < 0)return false;
        if($x >= count($this->map) || $y >= count($this->map[0]))return false;
        return $this->map[$x-1][$y-1] === 0;
    }

    public function IsInPointList($p, $point_list){ // 判斷點是否在某個集合中
        foreach($point_list as $point){
            if($p->x == $point->x && $p->y == $point->y)return true;
        }
        return false;
    }

    public function IsInOpenList($p){ // 判斷點是否在open_set中
        return $this->IsInPointList($p, $this->open_set);
    }

    public function IsInCloseList($p){ // 判斷點是否在clost_set中
        return $this->IsInPointList($p, $this->close_set);
    }

    public function setStartPoint($p){
        $this->start = new Point($p->x, $p->y);
    }

    public function IsStartPoint($p){ // 判斷點是否為起點
        return $this->start->x == $p.x && $this->start->y == $p->y;
    }

    public function setEndPoint($p){
        $this->end = new Point($p->x, $p->y);
    }

    public function IsEndPoint($p){ // 判斷點是否為終點
        return $this->end->x == $p->x && $this->end->y == $p->y;
    }

    public function RunAndSaveImage(){
        $start_p = new point(4, 2);
        $start_p->cost = 0;
        $this->setStartPoint($start_p);
        $this->open_set[] = $start_p;
        $this->setEndPoint(new point(5, 5));
        $t = 1;

        while(true){
            echo '<h1>Start: ' . $t . '</h1>';
            $index = $this->SelectPointInOpenList();
            if($index < 0){
                echo 'No path found, algorithm failed!!!<br />';
                return ;
            }
            // echo $index;
            // exit();
            $p = $this->open_set[$index];

            if(DEBUG){
                echo '------DEGUB-START------';
                var_dump($index);
                var_dump($p);
                echo '------DEBUG---END------<br/>';
            }

            if($this->IsEndPoint($p)){
                return $this->BuildPath($p);
            }
            $this->clost_set[] = $p;

            $x = $p->x;
            $y = $p->y;
            unset($this->open_set[$index]);
            
            

            $this->ProcessPoint($x-1, $y+1, $p); //左上
            $this->ProcessPoint($x-1, $y,   $p); //左
            $this->ProcessPoint($x-1, $y-1, $p); //左下
            $this->ProcessPoint($x,   $y-1, $p); //下
            $this->ProcessPoint($x+1, $y-1, $p); //右下
            $this->ProcessPoint($x+1, $y,   $p); //右
            $this->ProcessPoint($x+1, $y+1, $p); //右上
            $this->ProcessPoint($x,   $y+1, $p); //上
            if(DEBUG && false){
                echo '------DEGUB-START------';
                var_dump($this->open_set);
                echo '------DEBUG---END------<br/>';
            }


            echo '<h1>Over: ' . $t++ . '</h1>';
        }
    }

    public function ProcessPoint($x, $y, $parent){
        if($this->IsValidPoint($x, $y) == false){
            return ;
        }
        $p = new point($x, $y);
        if($this->IsInCloseList($p)){
            return ;
        }
        echo 'Process Point [' . $p->x . ', ' . $p->y . '], cost: ' . $p->cost . '<br />';
        if($this->IsInOpenList($p) == false){
            $p->parant = $parent;
            $p->cost = $this->TotalCost($p);
            array_push($this->open_set, $p);
        }
    }

    public function SelectPointInOpenList(){
        if(DEBUG && false){
            echo '------DEGUB-START------';
            var_dump($this->open_set);
            echo '------DEBUG---END------<br/>';
        }
        $index = 0;
        $selected_index = -1;
        $min_cost = PHP_INT_MAX;
        foreach($this->open_set as $index => $p){
            $cost = $this->TotalCost($p);
            if($cost < $min_cost){
                $min_cost = $cost;
                $selected_index = $index;
            }
            //$index += 1;
        }
        return $selected_index;
    }

    public function BuildPath($p){
        $path = array();
        while(true){
            array_push($path, $p);
            if($p != null || $this->IsStartPoint($p))break;
            else $p = $p->parent;
        }
        foreach($path as $_p){
            echo '(' . $_p->x . ', ' . $_p->y . ') ';
        }
        echo '<br />Algorithm finished';
    }
}

$searchPah = new AStar($_map);
$searchPah->RunAndSaveImage();