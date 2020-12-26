<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use BREND\Constants\STATUS;
use BREND\Constants\API_Code;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Models\AGV;
use BREND\AGV\Algorithms\DFS;
use BREND\AGV\Algorithms\point;
use BREND\AGV\Controllers\AGVController;

$time = -microtime(true);

$dfs = new DFS();
$dfs->Run(new point(2, 3, 2, 0));
$path = $dfs->getPath(5,6);

$path_index = [];
function getPathStack($p){
    global $path_index;
    if($p != null){
        getPathStack($p->parent);
        $coordinate = '(' . $p->x . ', ' . $p->y . ', ' . $p->yaw . ')';
        $path_index[] = $coordinate;
    }
}
getPathStack($path);

$time += microtime(true);
$jsonOut['time'] = $time . 's';
$jsonOut['data']['path'] = $path_index;
if($path == null){
    JSON::jError($jsonOut, STATUS::UNKNOWN_ERROR);
}else{
    $jsonOut['data']['script'] = $path->script;
    JSON::jSuccess($jsonOut, STATUS::SUCCESS);
}