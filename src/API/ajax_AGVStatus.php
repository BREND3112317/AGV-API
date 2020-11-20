<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Symfony\Component\HttpFoundation\Request;
use BREND\AGV\Controllers\AGVController;

$request = Request::createFromGlobals();

$AGV1 = new AGVController($request['Name']);

echo json_encode($AGV1->getStatus()->toArray());