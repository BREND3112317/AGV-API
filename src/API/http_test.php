<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use BREND\Constants\STATUS;
use BREND\Constants\API_Code;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Models\AGV;
use BREND\AGV\Controllers\AGVController;

//$post = new AGV_response(AGV_request::POST("ITRI_3-1", "500", array(), AGV_request::$url));
//$AGV1 = new AGVController("ITRI_3-1", "http://192.168.101.234:50100/AGV/Test");
// $AGV1 = new AGVController("ITRI_3-4");
// $jsonOut = [
//     'code' => STATUS::SUCCESS, 
//     'data' => $AGV1->getData()->toArray()
// ];
// $response = new Response();
// $response->setContent(json_encode($AGV1->getData()->toArray()));
// $response->headers->set('Content-Type', 'application/json');
// $response->setStatusCode(Response::HTTP_OK);
// $response->send();

//var_dump($AGV1->getStatus()->toArray());

//var_dump($AGV1->getMapPosition());


// var_dump(AGV_request::POST("ITRI_3-3", "500", array(), "http://59.124.226.9:6592/AGV/SendAgvCmd"));
// var_dump(AGV_request::POST("testName", "500", array(), "testUrl"));



$AGV = new AGV("ITRI_3-1", "http://192.168.101.234:50100/AGV/SendAgvCmd");
// $AGV = new AGVController("ITRI_3-4");
// $AGV = new AGV("ITRI_3-4");



$jsonOut = [
    'code' => STATUS::SUCCESS, 
    'data' => $AGV->getData()->toArray()
    //'data' => $AGV->getData()->toArray()
];
var_dump($jsonOut);
exit();
$response = new Response();
$response->setContent(json_encode($jsonOut));
$response->headers->set('Content-Type', 'application/json');
$response->setStatusCode(Response::HTTP_OK);
$response->send();
