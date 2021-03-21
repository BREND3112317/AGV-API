<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use BREND\Constants\STATUS;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Controllers\AGVController;

$request = Request::createFromGlobals();
$Data = json_decode($request->getContent(), true);
$jsonOut = $Data;
// throw new Exception("test");
// exit();

$AGV = new AGVController("ITRI_3-3", "http://59.124.226.9:6592/AGV/SendAgvCmd");
// $AGV = new AGVController($Data['Name']);


$jsonOut = [
    'code' => STATUS::SUCCESS, 
    'data' => $AGV->getData($Data['Cmd'])
];

$response = new Response();
$response->setContent(json_encode($jsonOut));
$response->headers->set('Content-Type', 'application/json');
$response->setStatusCode(Response::HTTP_OK);
$response->send();

exit();

?>