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

$AGV = new AGVController("ITRI_3-1");

//var_dump($AGV1->getStatus()->toArray());

//var_dump($AGV1->getMapPosition());


$jsonOut = [
    'code' => STATUS::SUCCESS, 
    'data' => $AGV->GoChargeing()
];

var_dump($jsonOut['data']);


//JSON::jSuccess($jsonOut, STATUS::SUCCESS);