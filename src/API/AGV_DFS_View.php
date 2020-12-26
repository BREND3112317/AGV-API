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
  <meta property="og:title" content="AGV - DFS" />
  <meta property="og:description" content="This Website created by BREND." />
  <meta property="og:image" content="/opengraph.png" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="AGV - DFS" />
  <meta name="twitter:description" content="This Website created by Brend." />
  <!--BootStrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
  <!--Main CSS-->
  
  <title>AGV - DFS</title>
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
        width: 80px;
        height: 80px;
    }

    .block {
        background: gray;
    }
  </style>
</head>
<body>
    <div class="container">
        <?php
        $time = -microtime(true);



        define("DEBUG", true);

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
            public $x, $y, $yaw;
            public $cost;
            public $parent;
            public $script = array();
            public function __construct($x, $y, $yaw, $cost = PHP_INT_MAX-1, $parent = null){
                $this->x    = $x;
                $this->y    = $y;
                $this->yaw  = $yaw;
                $this->cost = $cost;
                $this->parent = $parent;
            }
            public function setScript($script){
                $this->script = $script;
            }
            public function addScript($code){
                array_push($this->script, $code);
            }
            public function getLastScript(){
                return end($this->script);
            }
            public function changeLastScrtip($code){
                $this->script[key($this->script)] = $code;
            }
        }

        class DFS{

            public $charges = ['movestart' => 4.24, 'movecontinue' => 2.64, 'tra90' => 4.41, 'tra180' => 4.80];

            public $map = [ [-1,-2, 0, 0,-1,-1,-1],
                            [-1,-1, 0, 0, 0,-2,-2],
                            [-1,-1, 0, 0, 0,-2,-2],
                            [-1,-1, 0, 0, 0, 0,-2],
                            [-1, 0, 0, 0, 0, 0,-2],
                            [-1, 0, 0, 0, 0, 0,-1],
                            [-1, 0, 0, 0, 0, 0,-1],
            ];

            public $map_index = array();

            public $start;

            public function __construct($_map = null){
                if($_map!==null){
                    $this->map = $_map;
                }
            }

            // public function getYawGap($p, $yaw){
            //     $yaw_g = $yaw
            //     if(abs($cost_index) == 2){//迴轉(左回)
            //         $p->addScript('180');
            //         $p->addScript('10100');
            //     }else if($cost_index > 0 || $cost_index == -3){//右轉
            //         $p->addScript('150');
            //         $p->addScript('10100');
            //     }else if($cost_index < 0 || $cost_index == 3){//左轉
            //         $p->addScript('170');
            //         $p->addScript('10100');
            //     }
            // }

            public function calYaw($x, $y, $p){
                /* * * * *
                        上
                        1
                  左 2     0 右
                        3
                        下
                * * * * */
                if(($x == $p->x) == false){
                    return $x > $p->x ? 0 : 2;
                }else if(($y == $p->y) == false){
                    return $y > $p->y ? 3 : 1;
                }
            }

            public function estimateCost($p, $yaw){
                $yawCost = $this->Yawcost($p, $yaw);
                return $yawCost + (($yawCost == 0) && (count($p->script)>0) ? $this->charges['movecontinue'] : $this->charges['movestart']);
            }

            public function Yawcost($p, $yaw){ // 節點道起點的移動代價，對應上文的g(n)
                $cost_index = abs($p->yaw - $yaw);
                $tra_cost = ($cost_index == 0 ? 0 : ($cost_index == 2 ? $this->charges['tra180'] : $this->charges['tra90']));
                // 轉彎代價 + 行走代價
                // 假設沒有轉彎，就是接續行走，不用重新啟動"前進"
                return ($cost_index == 0 ? 0 : ($cost_index == 2 ? $this->charges['tra180'] : $this->charges['tra90']));
            }

            public function moveCost($p){
                if(count($p->script) == 0){
                    return $this->charges['movestart'];
                }else{
                    $move = intval($p->getLastScript());
                    if($move >10000 && $move < 15000){
                        return $this->charges['movecontinue'];
                    }else{
                        return $this->charges['movestart'];
                    }
                }
            }

            public function IsValidPoint($x, $y){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
                if($x < 0 || $y < 0)return false;
                if($x >= count($this->map) || $y >= count($this->map[0]))return false;
                return $this->map[$y][$x] === 0;
            }

            public function setStartPoint($p){ // 設定起點
                $this->start = new Point($p->x, $p->y, $p->yaw, 0);
            }

            public function IsStartPoint($x, $y){ // 判斷點是否為起點
                return $this->start->x == $x && $this->start->y == $y;
            }

            public function Run($start_p){
                $this->setStartPoint($start_p);
                $this->map_index[$start_p->y][$start_p->x] = $start_p;

                $this->ProcessPoint($start_p->x,    $start_p->y+1,  $start_p);
                $this->ProcessPoint($start_p->x-1,  $start_p->y,    $start_p);
                $this->ProcessPoint($start_p->x,    $start_p->y-1,  $start_p);
                $this->ProcessPoint($start_p->x+1,  $start_p->y,    $start_p);
            }

            public function ProcessPoint($x, $y, $parent){
                if($this->IsValidPoint($x, $y)==false || $this->IsStartPoint($x, $y)) return;
                $newYaw = $this->calYaw($x, $y, $parent);
                $newCost = $parent->cost + $this->estimateCost($parent, $newYaw);
                
                if(isset($this->map_index[$y][$x])==false || $this->map_index[$y][$x]->cost > $newCost){
                    $p = new point($x, $y, $newYaw, $newCost, $parent);
                    $p->setScript($parent->script);
                    $cost_index = $parent->yaw - $newYaw;
                    if(abs($cost_index) == 2){//迴轉(左回)
                        $p->addScript('180');
                        $p->addScript('10100');
                    }else if($cost_index > 0 || $cost_index == -3){//右轉
                        $p->addScript('150');
                        $p->addScript('10100');
                    }else if($cost_index < 0 || $cost_index == 3){//左轉
                        $p->addScript('170');
                        $p->addScript('10100');
                    }else{
                        if(count($p->script) == 0){
                            $p->addScript('10100');
                        }else{
                            $move = intval($p->getLastScript());
                            if($move >10000 && $move<15000){
                                $move+=100;
                                $p->changeLastScrtip($move);
                            }else{
                                $p->addScript('10100');
                            }
                        }
                        
                    }
                    $this->map_index[$y][$x] = $p;
                    //$this->ShowView();
                    $this->ProcessPoint($p->x,      $p->y+1,    $p);
                    $this->ProcessPoint($p->x-1,    $p->y,      $p);
                    $this->ProcessPoint($p->x,      $p->y-1,    $p);
                    $this->ProcessPoint($p->x+1,    $p->y,      $p);
                }
            }

            public function BuildPath($p){
                $path = array();
                echo "Path: ";
                $cost = 0;
                $index = 0;
                $_p = $p;
                
                while($_p!=null){
                    // if(DEBUG){
                    //     echo "====DEBUG-BUILDPATH====<br/>";
                    //     var_dump($p);
                    //     echo "<br/>====DEBUG-BUILDPATH====<br/>";
                    // }
                    echo '(' . $_p->x . ', ' . $_p->y . ', ' . $_p->yaw . ') -> ';
                    array_push($path, $_p);
                    $cost += $_p->cost;
                    $_p = $_p->parent;
                }
                echo "cost=$cost<br/>";
                echo "Script: ";
                foreach($p->script as $s){
                    echo '"' . $s . '" ';
                }
                echo '<br/>';
                $this->ShowView($path);
            }

            public function ShowView($path = array(), $_map = null){
                if($_map == null){
                    $_map = $this->map;
                }
                $index = count($path);
                foreach($path as $p){
                    //$_map[$p->y][$p->x] = $p->cost;
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
                            echo '<td style="background-color: rgba(255, 0, 0, ' . $_map[$i][$j]/(count($path)+1) . ');">';
                            if(isset($this->map_index[$i][$j])){
                                echo $this->map_index[$i][$j]->cost; 
                            }else{
                                echo $_map[$i][$j];
                            }
                            echo '</td>';
                        }else if(isset($this->map_index[$i][$j])){
                            echo '<td>' . $this->map_index[$i][$j]->cost . '</td>';                            
                        }else{
                            echo '<td>' . $_map[$i][$j] . '</td>';
                        }
                    }
                    echo '</tr>';
                }
            }
        }

        $bfs = new DFS();
        $bfs->Run(new point(2, 4, 2, 0));
        //$bfs->ShowView();
        $bfs->BuildPath($bfs->map_index[6][5]);

        $time += microtime(true);

        echo "<br/><h2>Do php in $time seconds<h2>\n";
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