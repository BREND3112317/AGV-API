<?php
$_map = [    [-1,-2, 0, 0,-1,-1,-1],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0,-2,-2],
            [-1,-1, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-2],
            [-1, 0, 0, 0, 0, 0,-1],
            [-1, 0, 0, 0, 0, 0,-1],
];


trait point{
    public $x, $y;
    public function __construct($x, $y){
        $this->x = $x;
        $this->y = $y;
    }
}

// class AGV extends point{
//     public function CalculationCost(){

//     }

    
// }

class SearchPath{
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

    public $open_set, $close_set;

    public function __construct($map){
        $this->map = $map;
    }

    public function BaseCost($p){ // 節點道起點的移動代價，對應上文的g(n)
        $this->x_dis = $p->x;
        $this->y_dis = $p->y;
        return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis);
    }

    public function HeuristicCost($p){ // 節點到終點的啟發函數，對應上文的h(n)。於是我們基於網格的圖形，所以這個函數和上一個函數用的對角距離
        $this->x_dis = count($map) - 1 - $p->x;
        $this->y_dis = count($map[0]) - 1 - $p->y;
        return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis);
    }

    public function TotalCost($p){ // 代價總和，也是上面提到的f(n)
        return $this->BaseCost($p) + $this->HeuristicCost($p);
    }

    public function IsValidPoint($p){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
        if($p->x < 0 || $p->y < 0)return false;
        if($p->x >= count($map) || $p->y >= count($map[0]))return false;
        return $map[$p->x-1][$p->y-1] !== 0;
    }

    public function IsInPointList($p, $point_list){ // 判斷點是否在某個集合中
        foreach($point_list as $point){
            if($p->x == $point->x && $p->y == $point->y)return true;
        }
        return false;
    }

    public function IsInOpenList($p){ // 判斷點是否在open_set中
        return IsInPointList($p, $this->open_set);
    }

    public function IsInCloseList($p){ // 判斷點是否在clost_set中
        return IsInPointList($p, $this->close_set);
    }

    public function IsStartPoint($p){ // 判斷點是否為起點
        return $this->start->x == $p.x && $this->start->y == $p->y;
    }

    public function IsEndPoint($p){ // 判斷點是否為終點
        return $this->end->x == $p.x && $this->end->y == $p->y;
    }

    public $width = 7, $height = 7;
    public function showMap(){
        for($i=0;$i<count($this->map);++$i){
            for($j=0;$j<count($this->map[$i]);++$j){
                echo $this->map[$i][$j] . ' ';
            }
            echo '<br />';
        }
    }

    public function getPath($start_x, $start_y, $end_x, $end_y){
        $this->showMap();
        echo "(" . ($start_x) . ", " . ($start_y) . ")<br /><br />";
        if(($start_x>=0 && $start_x<$this->width && $start_y>=0 && $start_y<$this->height) == false || $this->map[$start_x][$start_y]!=0)return ;
        $index_x = $start_x<$end_x ? ($start_x==$end_x ? 0 : 1) : -1;
        $index_y = $start_y<$end_y ? ($start_y==$end_y ? 0 : 1) : -1;
        $this->map[$start_x][$start_y]++;
        $this->getPath($start_x+$index_x, $start_y, $end_x, $end_y);
        $this->getPath($start_x, $start_y+$index_y, $end_x, $end_y);
        $this->map[$start_x][$start_y]++;
    }
}
$searchPah = new SearchPath();
$searchPah->getPath(4, 2, 5, 6);