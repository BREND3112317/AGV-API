<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Symfony\Component\HttpFoundation\Request;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Controllers\AGVController;

//$post = new AGV_response(AGV_request::POST("ITRI_3-1", "500", array(), AGV_request::$url));
//$AGV1 = new AGVController("ITRI_3-1", "http://192.168.101.234:50100/AGV/Test");
$AGV1 = new AGVController("ITRI_3-4");

var_dump($AGV1->getStatus()->toArray());

//var_dump($AGV1->getMapPosition());