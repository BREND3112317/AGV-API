<?php

namespace BREND\Constants;

class STATUS{
    const SUCCESS               = 0;

    //-----------STATUS--------------

    //-----------ERROR---------------
    const UNKNOWN_ERROR         = -1;
    const RESPONSE_ERROR        = -100;
    const JSON_NO_REQUIRED_DATA = -200;
    const JSON_FORMAT_ERROR     = -201;

    const statusTexts  = array(
        0   => "正常",
        1   => "異常",
        200  => "JSON Key不存在",
    );
}
