<?php

namespace BREND\Constants;

class AGVSTATUS{
    const SUCCESS               = 0;

    //-----------STATUS--------------
    const AGV_IS_BUSY           = 900;
    const AGV_NAME_UNEXIST      = 902;
    const AGV_CONNECT_ERROR1    = 905;
    const AGV_CONNECT_ERROR2    = 906;
    const AGV_CONNECT_ERROR3    = 907;
    const AGV_CONNECT_ERROR4    = 908;

    const PARAM_INVALID         = 909;
    const JSON_FORMATE_INVALID  = 910;
    const AGV_CMD_ERROR         = 911;
    const AGV_PARAM_ERROR       = 912;
    const WEB_SERVICE_ERROR     = 921;

    const statusTexts  = array(
        0   => "正常",
        900 => "AGV忙碌中",
        902 => "AGV名稱不存在",
        905 => "AGV連線異常",
        909 => "無效參數",
        910 => "JSON格式錯誤",
        911 => "AGV命令代碼錯誤",
        912 => "AGV參數錯誤",
        921 => "Web Service異常",
    );
    public static function GetMessage($statusCode){
        if(isset(self::$statusTexts[$statusCode])){
            return self::$statusTexts[$statusCode];
        }
        return null;
    }
}
