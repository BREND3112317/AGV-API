<?php

namespace BREND\AGV\Filters;

class AGVFilter{

    public static function Response($statusCode, $Config){
        return ['StatusCode' => $statusCode, 'Config'=> $Config];
    }
}