<?php

namespace BREND\AGV\Models;

use Symfony\Component\HttpClient\HttpClient;

class AGV_request{
    public static $url = "http://59.124.226.9:6592/AGV/SendAgvCmd";

    public static function POST($name, $cmd, $param, $url){
        if(is_array($param) == false){
            $param = array($param);
        }
        $httpClient = HttpClient::create();
        $response = $httpClient->request(
            "POST",
            $url,
            [
                'headers' => [
                    "Accept" => "application/json",
                ],
                'json' => [
                    "Name" => $name . "",
                    "Cmd" => $cmd . "",
                    "Param" => $param
                ]
            ]
        );

        $httpCode = $response->getStatusCode();
        $httpContentType = $response->getHeaders()['content-type'][0];
        $httpContent = json_decode($response->getContent(), true);
        return $httpContent;
    }
}