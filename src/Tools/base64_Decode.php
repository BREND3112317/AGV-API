<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

try{
    echo base64url_decode($request->query->get('code'));
}catch (\Throwable $t){

}
