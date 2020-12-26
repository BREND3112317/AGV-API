<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BREND\Constants\STATUS;

$jsonOut = array();

class JSON{
    public static function prepareJson($jsonIn, $segment){
        //TODO: check all exception to handle
        if (!self::isJson($jsonIn)) {
            self::jError($jsonOut, STATUS::JSON_FORMAT_ERROR);
        }
        $jsonIn = json_decode($jsonIn, true);
        if (!array_key_exists($segment, $jsonIn)) 
        {
            self::jError($jsonOut, STATUS::JSON_NO_REQUIRED_DATA);
        }
        $data = $jsonIn[$segment];
        

        return $data;
    }

    public static function jError(&$jsonOut, $code)
    {
        $jsonOut['status'] = 'failed';
        if (!isset($jsonOut['code']) || !is_array($jsonOut['code'])) $jsonOut['code'] = array();
        array_push($jsonOut['code'], $code);
        self::json_exit($jsonOut);
    }

    public static function jSuccess(&$jsonOut, $code)
    {
        $jsonOut['status'] = 'success';
        if (!isset($jsonOut['code']) || !is_array($jsonOut['code'])) $jsonOut['code'] = array();
        array_push($jsonOut['code'], $code);
        self::json_exit($jsonOut);
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function json_exit($_jsonOut = null)
    {
        if($_jsonOut!=null){
            $jsonOut = $_jsonOut;
        }
        $response = new Response();
        $response->setContent(json_encode($jsonOut));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(Response::HTTP_OK);
        $response->send();
        exit;
    }
}