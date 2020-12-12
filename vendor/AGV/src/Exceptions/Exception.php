<?php

namespace BREND\AGV\Exceptions;

use BREND\Constants\STATUS;

class Exception extends \Exception{
    public function __construct($message, $code=STATUS::UNKNOWN_ERROR, Throwable $previous=null){
        parent::__construct($message, $code, $previous);
    }
}