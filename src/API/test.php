<?php
$time = -microtime(true);
function absAngle($angle){
    while($angle<0)$angle+=360;
    $angle = round($angle/90)*90;
    return $angle%360;
}
function compareDFSYaw($yaw){
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
$time += microtime(true);

echo "<br/><h2>Do php in $time seconds<h2>\n";
?>