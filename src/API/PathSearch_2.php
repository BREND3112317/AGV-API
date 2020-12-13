<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="/favicon.ico" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="theme-color" content="#343a40" />
  <meta name="description" content="This Website created by BREND." />
  <!-- <meta property="og:url" content="https://boylin0.github.io" /> -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="AGV GUI test" />
  <meta property="og:description" content="This Website created by BREND." />
  <meta property="og:image" content="/opengraph.png" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="AGV GUI test" />
  <meta name="twitter:description" content="This Website created by Brend." />
  <!--BootStrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <!--Main CSS-->
  
  <title>AGV GUI test - 1</title>
  <style>
    table {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
    }
    
    table, th, td {
        margin: 0px;
        border: 1px solid black;
    }

    th, td {
        width: 36px;
        height: 36px;
    }

    .block {
        background: gray;
    }
  </style>
</head>
<body>
    <div class="container">
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
            public $x, $y, $yaw, $cost;
            public $parent;
            public function __construct($_x, $_y, $_yaw = 0, $_cost = PHP_INT_MAX-1){
                $this->x = $_x;
                $this->y = $_y;
                $this->yaw = $_yaw;
                $this->cost = $_cost;
            }
        }

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

            public $x_dis, $y_dis, $yaw_dis;

            public $open_set = array(), $close_set = array();

            public function __construct($map){
                $this->map = $map;
            }

            public function BaseCost($p){ // 節點道起點的移動代價，對應上文的g(n)
                $this->x_dis = $p->x;
                $this->y_dis = $p->y;
                return $this->x_dis + $this->y_dis;
                // return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis); //对角距离
            }

            public function HeuristicCost($p){ // 節點到終點的啟發函數，對應上文的h(n)。於是我們基於網格的圖形，所以這個函數和上一個函數用的對角距離
                $this->x_dis = count($this->map) - 1 - $p->x;
                $this->y_dis = count($this->map[0]) - 1 - $p->y;
                return $this->x_dis + $this->y_dis;
                // return $this->x_dis + $this->y_dis + (sqrt(2)-2)*min($this->x_dis, $this->y_dis); //对角距离
            }

            // public function yawCost($p){
            //     $this->yaw_dis = 
            // }

            public function TotalCost($p){ // 代價總和，也是上面提到的f(n)
                $cost = $this->BaseCost($p) + $this->HeuristicCost($p);
                // if(DEBUG){
                //     echo '------DEGUB-START------<br />';
                //     print_r($p);
                //     echo '<br/>';
                //     print_r($cost);
                //     echo '<br/>';
                //     echo '------DEBUG---END------<br/>';
                // }
                return $cost;
            }

            public function IsValidPoint($x, $y){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
                if($x < 0 || $y < 0)return false;
                if($x >= count($this->map) || $y >= count($this->map[0]))return false;
                //echo "(" . $x . ", " . "): " . $this->map[$x][$y] . '<br/>';
                return $this->map[$y][$x] === 0;
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
                // if(DEBUG){
                //     echo '------DEGUB-START------<br />';
                //     var_dump($p);
                //     echo '<br/>';
                //     var_dump($this->start);
                //     echo '<br/>';
                //     echo '------DEBUG---END------<br/>';
                // }
                return $this->start->x == $p->x && $this->start->y == $p->y;
            }

            public function setEndPoint($p){
                $this->end = new Point($p->x, $p->y);
            }

            public function IsEndPoint($p){ // 判斷點是否為終點
                return $this->end->x == $p->x && $this->end->y == $p->y;
            }

            public function RunAndSaveImage($start_x, $start_y, $end_x, $end_y){
                $start_p = new point($start_x, $start_y);
                $start_p->cost = 0;
                $this->setStartPoint($start_p);
                $this->open_set[] = $start_p;
                $this->setEndPoint(new point($end_x, $end_y));
                $t = 1;

                while(true){
                    //echo '<h1>Start: ' . $t . '</h1>';
                    $index = $this->SelectPointInOpenList();
                    if($index < 0){
                        echo 'No path found, algorithm failed!!!<br />';
                        return ;
                    }
                    // echo $index;
                    // exit();
                    $p = $this->open_set[$index];

                    // if(DEBUG){
                    //     echo '------DEGUB-START------';
                    //     var_dump($index);
                    //     var_dump($p);
                    //     echo '------DEBUG---END------<br/>';
                    // }

                    if($this->IsEndPoint($p)){
                        return $this->BuildPath($p);
                    }
                    $this->clost_set[] = $p;

                    $x = $p->x;
                    $y = $p->y;
                    unset($this->open_set[$index]);
                    
                    

                    //$this->ProcessPoint($x-1, $y+1, $p); //左上
                    $this->ProcessPoint($x-1, $y,   $p); //左
                    //$this->ProcessPoint($x-1, $y-1, $p); //左下
                    $this->ProcessPoint($x,   $y-1, $p); //下
                    //$this->ProcessPoint($x+1, $y-1, $p); //右下
                    $this->ProcessPoint($x+1, $y,   $p); //右
                    //$this->ProcessPoint($x+1, $y+1, $p); //右上
                    $this->ProcessPoint($x,   $y+1, $p); //上
                    // if(DEBUG && false){
                    //     echo '------DEGUB-START------';
                    //     var_dump($this->open_set);
                    //     echo '------DEBUG---END------<br/>';
                    // }


                    //echo '<h1>Over: ' . $t++ . '</h1>';
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
                //echo 'Process Point [' . $p->x . ', ' . $p->y . '], cost: ' . $p->cost . '<br />';
                if($this->IsInOpenList($p) == false){
                    $p->parent = $parent;
                    $p->cost = $this->TotalCost($p);
                    array_push($this->open_set, $p);
                }
            }

            public function SelectPointInOpenList(){
                // if(DEBUG && false){
                //     echo '------DEGUB-START------';
                //     var_dump($this->open_set);
                //     echo '------DEBUG---END------<br/>';
                // }
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

            public function ShowView($path, $_map = null){
                if($_map == null){
                    $_map = $this->map;
                }
                $index = count($path);
                foreach($path as $p){
                    $_map[$p->y][$p->x] = $index--;
                }
                echo "<table><tbody>";
                echo '<tr>';
                echo '<td></td>';
                for($i=1;$i<=count($_map);++$i){
                    echo '<td style="background-color: rgba(0, 255, 0, 0.7);">' . ($i-1) . '</td>';
                }
                echo '</tr>';
                for($i=0;$i<count($_map);++$i){
                    echo '<tr>';
                    echo '<td style="background-color: rgba(0, 255, 0, 0.7);">' . ($i) . '</td>';
                    for($j=0;$j<count($_map[$i]);++$j){
                        if($_map[$i][$j]<0){
                            echo '<td class="block">' . $_map[$i][$j] . '</td>';
                        }else if($_map[$i][$j]>0){
                            echo '<td style="background-color: rgba(255, 0, 0, ' . $_map[$i][$j]/count($path) . ');">' . $_map[$i][$j] . '</td>';
                        }else{
                            echo '<td>' . $_map[$i][$j] . '</td>';
                        }
                    }
                    echo '</tr>';
                }
            }

            public function BuildPath($p){
                $path = array();
                while(true){
                    // if(DEBUG){
                    //     echo '------DEGUB-START------<br />';
                    //     var_dump($p);
                    //     echo '------DEBUG---END------<br/>';
                    // }
                    array_push($path, $p);
                    if($this->IsStartPoint($p))break;
                    else $p = $p->parent;
                }
                $this->ShowView($path);
                foreach($path as $_p){
                    echo '(' . $_p->x . ', ' . $_p->y . ') ';
                }
                echo '<br />Algorithm finished';
            }
        }

        $searchPah = new AStar($_map);
        $searchPah->RunAndSaveImage(4, 2, 1, 4);
        var_dump(realpath_cache_size());
        ?>
    </div>
</body>
<!--Bootstrap JS-->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<!--JQuery-->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!--Fontawesome-->
<script src="https://kit.fontawesome.com/08225bb003.js" crossorigin="anonymous"></script>

</html>