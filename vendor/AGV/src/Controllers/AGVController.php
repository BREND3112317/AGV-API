<?php

namespace BREND\AGV\Controllers;

use BREND\Constants\STATUS;
use BREND\Constants\AGVSTATUS;
use BREND\Constants\API_Code;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Models\AGV;
use BREND\AGV\Exceptions\AGVException;

class AGVController{
    protected $api_url;
    protected $AGV_name;

    public function __construct($name, $url = null){
        $this->AGV_name = $name;
        $this->api_url = ($url == null ? AGV_request::$url : $url);
    }

    public function Getaway($code, $param = array()){
        $AGV = new AGV($this->AGV_name, $this->api_url);
        switch($code){
            case API_Code::DIRECTSTOP:
                return $AGV->DirectSTOP();
                break;
            case API_Code::SCRIPTOVER:
                return $AGV->ScriptOVER();
                break;
            case API_Code::SCRIPTContinue:
                return $AGV->ScriptContinue();
                break;
            case API_Code::SCRIPTSTOP:
                return $AGV->ScriptSTOP();
                break;
            case API_Code::RUN1000:
                return $AGV->move(1000);
                break;
            case API_Code::TURNLEFT:
                return $AGV->spinLeft(90);
                break;
            case API_Code::TURNRIGHT:
                return $AGV->spinRight(90);
                break;
            case API_Code::SHELFUP:
                return $AGV->shelfup();
                break;
            case API_Code::SHELFDOWN:
                return $AGV->shelfdown();
                break;
            // case API_Code::TURNRIGHT:
            //     return $AGV->spinRight(90);
            //     break;
            // case API_Code::TURNRIGHT:
            //     return $AGV->spinRight(90);
            //     break;
        }
    }

    public function getData($code = API_Code::ALL){
        $AGV = new AGV($this->AGV_name, $this->api_url);
        switch($code){
            case API_Code::ALL:
                return $AGV->getData()->toArray();
                break;
            case API_Code::BATTERY:
                return $AGV->getBattery()->toArray();
                break;
            case API_Code::POS:
                return $AGV->getMapPosition()->toArray();
                break;
            case API_Code::IsLeftUp:
                return $AGV->checkIsLeftUp()->toArray();
                break;
            case API_Code::POS:
                return $AGV->getMapPosition()->toArray();
                break; 
        }
        
    }

    public function GoCharge(){
        $AGV = new AGV($this->AGV_name, $this->api_url);
        //if($AGV->getData()->)
    }

    private function absAngle($angle){
        while($angle<0)$angle+=360;
        return $angle%360;
    }
    public function spin($degree, $lockingDisc = true){ // 旋轉 : lockingDisc鎖定圓盤不動
        throw Exception(__FUNCTION__ . "is unactive.");
    }
}