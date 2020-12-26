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
// var_dump($Data);
// exit();

$AGV = new AGVController($Data['Name'], "http://192.168.101.234:50100/AGV/SendAgvCmd");
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