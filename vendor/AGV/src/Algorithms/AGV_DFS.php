<?php

namespace BREND\AGV\Algorithms;

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
        $this->script[key($this->script)] = $code . '';
    }
}

class AGV_DFS{
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

    public function absAngle($angle){
        while($angle<0)$angle+=360;
        $angle = round($angle/90)*90;
        return $angle%360;
    }

    public function compareDFSYaw($yaw){
        switch($yaw){
            case 0:
                return 1;
                break;
            case 1:
                return 0;
                break;
            case 2:
                return 3;
                break;
            case 3:
                return 2;
                break;
        }
    }

    public function compareAGVYaw($yaw){
        switch($yaw){
            case 1:
                return 0;
                break;
            case 0:
                return 90;
                break;
            case 3:
                return 180;
                break;
            case 2:
                return 270;
                break;
        }
    }
    
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

    public function IsValidPoint($x, $y){ // 判斷點是否有效，不再地圖內或障礙物都是無效的
        if($x < 0 || $y < 0)return false;
        if($x >= count($this->map) || $y >= count($this->map[0]))return false;
        return $this->map[$y][$x] === 0;
    }

    public function setStartPoint($code, $yaw){ // 設定起點
        $x = intval($code['4']);
        $y = intval($code['1']);
        $yaw = $this->compareDFSYaw($this->absAngle($yaw)/90);
        $this->start = new Point($x, $y, $yaw, 0);
    }

    public function IsStartPoint($x, $y){ // 判斷點是否為起點
        return $this->start->x == $x && $this->start->y == $y;
    }

    public function Run(){
        $this->map_index['0'.$this->start->y.'00'.$this->start->x.'0'] = $this->start;

        $this->ProcessPoint($this->start->x,    $this->start->y+1,  $this->start);
        $this->ProcessPoint($this->start->x-1,  $this->start->y,    $this->start);
        $this->ProcessPoint($this->start->x,    $this->start->y-1,  $this->start);
        $this->ProcessPoint($this->start->x+1,  $this->start->y,    $this->start);
    }

    public function ProcessPoint($x, $y, $parent){
        if($this->IsValidPoint($x, $y)==false || $this->IsStartPoint($x, $y)) return;
        $newYaw = $this->calYaw($x, $y, $parent);
        $newCost = $parent->cost + $this->estimateCost($parent, $newYaw);
        
        if(isset($this->map_index['0'.$y.'00'.$x.'0'])==false || $this->map_index['0'.$y.'00'.$x.'0']->cost > $newCost){
            $p = new point($x, $y, $newYaw, $newCost, $parent);
            $p->setScript($parent->script);
            $cost_index = $parent->yaw - $newYaw;
            if(abs($cost_index) == 2){//迴轉(左回)
                $p->addScript('180');
                $p->addScript('10100');
            }else if((($cost_index > 0) && ($cost_index != 3)) || ($cost_index == -3)){//右轉
                $p->addScript('150');
                $p->addScript('10100');
            }else if((($cost_index < 0) && ($cost_index != -3)) || $cost_index == 3){//左轉
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
            $this->map_index['0'.$y.'00'.$x.'0'] = $p;
            //$this->ShowView();
            $this->ProcessPoint($p->x,      $p->y+1,    $p);
            $this->ProcessPoint($p->x-1,    $p->y,      $p);
            $this->ProcessPoint($p->x,      $p->y-1,    $p);
            $this->ProcessPoint($p->x+1,    $p->y,      $p);
        }
    }

    public function showScript($p){
        foreach($p->script as $s){
            echo '"' . $s . '" ';
        }
    }

    public function getScript($code){
        if(isset($this->map_index[$code])){
            return $this->map_index[$code]->script;
        }
        return null;
    }

    public function showPreviewPath($code){
        $path = $this->getCodePath($code);
        while($path != null){
            echo "0" . $path->y . "00" . $path->x . "0";
            echo " -> ";
            $path = $path->parent;
        }
    }

    public function getPath($x, $y){
        if(isset($this->map_index['0'.$y.'00'.$x.'0'])){
            return $this->map_index['0'.$y.'00'.$x.'0'];
        }
        return null;
    }

    public function getCodePath($code){
        if(isset($this->map_index[$code])){
            return $this->map_index[$code];
        }
        return null;
    }

    public function getAGVPreviewPath(){
        $paths = array();
        foreach($this->map_index as $code => $p){
            $path = array();
            $p=$p->parent;
            while($p != null){
                //echo '(' . $x->x . ', ' . $x->y . ') -> ';
                array_push($path, '0'.$p->y.'00'.$p->x.'0');
                $p = $p->parent;
            }
            $paths[$code] = $path;
        }
        return $paths;
    }
}
