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

// $AGV = new AGVController("ITRI_3-1");
//$AGV1 = new AGVController("ITRI_3-3");


//var_dump($AGV1->getStatus()->toArray());

//var_dump($AGV1->getMapPosition());

// var_dump(AGV_request::POST("ITRI_3-3", "500", array(), "http://59.124.226.9:6592/AGV/SendAgvCmd"));
// var_dump(AGV_request::POST("testName", "500", array(), "testUrl"));

$AGV = new AGV("ITRI_3-4", "http://59.124.226.9:6592/AGV/SendAgvCmd");
// $AGV = new AGV("ITRI_3-4");

$jsonOut = [
    'code' => STATUS::SUCCESS, 
    'data' => $AGV->Trans2AbsYaw(90)
];

var_dump($jsonOut['data']);

// $jsonOut = [
//     'code' => STATUS::SUCCESS, 
//     'data' => $AGV->getData()->toArray()
//     //'data' => $AGV->getData()->toArray()
// ];
// var_dump($jsonOut);
// exit();


//JSON::jSuccess($jsonOut, STATUS::SUCCESS);