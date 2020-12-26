<?php

use BREND\Constants\STATUS;
use Symfony\Component\HttpFoundation\Response;


set_exception_handler(function(Throwable $e){
    // //echo get_class($e);
    header('Content-Type: text/html');
    echo <<<HTML
    <h1>{$e->getMessage()}</h1>
    <p>{$e}</p>
    <strong>Code: </strong>{$e->getCode()}<br/>
    <strong>File: </strong> <code>{$e->getFile()} ({$e->getLine()})</code>
    <h3>Call Stack</h3>
    <pre>{$e->getTraceAsString()}<pre>
    HTML;
    die;
    exit();

    
    
    // $errorStatus['statusCode'] = $e->getCode();
    // $errorStatus['title'] = $e->getMessage();
    // $errorStatus['callStack'] = $e->getTraceAsString();
    // $jsonOut['errorEncode'] = base64url_encode(json_encode($errorStatus));
    $errorStatus = <<<HTML
    <h1>{$e->getMessage()}</h1>
    <p>{$e}</p>
    <strong>Code: </strong>{$e->getCode()}<br/>
    <strong>File: </strong> <code>{$e->getFile()} ({$e->getLine()})</code>
    <h3>Call Stack</h3>
    <pre>{$e->getTraceAsString()}<pre>
    HTML;
    $jsonOut['code'] = STATUS::UNKNOWN_ERROR;
    $jsonOut['data'] = base64url_encode($errorStatus);
    $response = new Response();
    $response->setContent(json_encode($jsonOut));
    $response->headers->set('Content-Type', 'application/json');
    $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    $response->send();
});