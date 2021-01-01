<?php

use BREND\Constants\STATUS;
use Symfony\Component\HttpFoundation\Response;

define("DEBUG", false);

set_exception_handler(function(Throwable $e){
    $errorStatus = <<<HTML
    <h1>{$e->getMessage()}</h1>
    <p>{$e}</p>
    <strong>Code: </strong>{$e->getCode()}<br/>
    <strong>File: </strong> <code>{$e->getFile()} ({$e->getLine()})</code>
    <h3>Call Stack</h3>
    <pre>{$e->getTraceAsString()}<pre>
    HTML;
    if(DEBUG==true){
        // //echo get_class($e);
        header('Content-Type: text/html');
        echo $errorStatus;
        die;
        exit();
    }else{
        // $jsonOut['errorEncode'] = base64url_encode(json_encode($errorStatus));
        
        $data['statusCode'] = $e->getCode();
        $data['title'] = $e->getMessage();
        $data['file']['name'] = $e->getFile();
        $data['file']['line'] = $e->getLine();
        $data['callStack'] = $e->getTraceAsString();
        $jsonOut = [
            'code' => STATUS::UNKNOWN_ERROR, 
            'data' => $data
            // 'data' => base64url_encode(json_encode($errorStatus))
        ];

        
        $response = new Response();
        $response->setContent(json_encode($jsonOut));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->send();
        exit();
    }
});