<?php

namespace BREND\AGV\Exceptions;

class AGVException extends Exception{

    public function __construct($message, $code=STATUS::UNKNOWN_ERROR, Throwable $previous=null){
        parent::__construct($message, $code, $previous);
    }
}