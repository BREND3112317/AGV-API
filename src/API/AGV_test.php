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

$AGV = new AGVController("ITRI_3-3", "http://59.124.226.9:6592/AGV/SendAgvCmd");
// $AGV = new AGV("ITRI_3-4");

$jsonOut = [
    'code' => STATUS::SUCCESS, 
    'data' => $AGV->Trans2AbsYaw(90)
];

var_dump($jsonOut['data']);


//JSON::jSuccess($jsonOut, STATUS::SUCCESS);