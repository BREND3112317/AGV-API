<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\Constants\STATUS;

$jsonOut = array();

class JSON{
    public static function prepareJsonIn($jsonIn, $segment){
        //TODO: check all exception to handle
        if (!self::isJson($jsonIn)) {
            self::jError(STATUS::JSON_FORMAT_ERROR);
        }
        $jsonIn = json_decode($jsonIn, true);
        if (!array_key_exists($segment, $jsonIn)) 
        {
            self::jError(STATUS::JSON_NO_REQUIRED_DATA);
        }
        $data = $jsonIn[$segment];
        

        return $data;
    }

    public static function prepareJsonOut($jonsOut, $data){
        
    }

    public static function jError($code, $jsonOut = array())
    {
        $jsonOut['status'] = 'failed';
        if (!isset($jsonOut['code']) || !is_array($jsonOut['code'])) $jsonOut['code'] = array();
        array_push($jsonOut['code'], $code);
        $response = new Response();
        $response->setContent(json_encode($jsonOut));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->send();
    }

    public static function jSuccess($code, $jsonOut = array())
    {
        $jsonOut['status'] = 'success';
        if (!isset($jsonOut['code']) || !is_array($jsonOut['code'])) $jsonOut['code'] = array();
        array_push($jsonOut['code'], $code);
        $response = new Response();
        $response->setContent(json_encode($jsonOut));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        $response->send();
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}