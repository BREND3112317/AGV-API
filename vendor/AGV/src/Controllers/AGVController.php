<?php

namespace BREND\AGV\Controllers;

use BREND\Constants\STATUS;
use BREND\Constants\AGVSTATUS;
use BREND\Constants\API_Code;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Models\AGV;
use BREND\AGV\Algorithms\DFS;
use BREND\AGV\Algorithms\point;
use BREND\AGV\Exceptions\AGVException;

class AGVController{
    protected $api_url;
    protected $AGV_name;

    private $AGV;

    public function __construct($name, $url = null){
        $this->AGV_name = $name;
        $this->api_url = ($url == null ? AGV_request::$url : $url);
        $this->AGV = new AGV($this->AGV_name, $this->api_url);
    }

    public function Getaway($code, $param = array()){
        switch($code){
            case API_Code::DIRECTSTOP:
                return $this->AGV->DirectSTOP();
                break;
            case API_Code::SCRIPTOVER:
                return $this->AGV->ScriptOVER();
                break;
            case API_Code::SCRIPTContinue:
                return $this->AGV->ScriptContinue();
                break;
            case API_Code::SCRIPTSTOP:
                return $this->AGV->ScriptSTOP();
                break;
            case API_Code::RUN1000:
                return $this->AGV->move(1000);
                break;
            case API_Code::TURNLEFT:
                return $this->AGV->spinLeft(90);
                break;
            case API_Code::TURNRIGHT:
                return $this->AGV->spinRight(90);
                break;
            case API_Code::TURNBACK:
                return $this->AGV->spinRight(180);
                break;
            case API_Code::SHELFUP:
                return $this->AGV->shelfup();
                break;
            case API_Code::SHELFDOWN:
                return $this->AGV->shelfdown();
                break;
            case API_Code::PLUGIN:
                return $this->AGV->PlugIn();
                break;
            case API_Code::PLUGOUT:
                return $this->AGV->PlugOut();
                break;
            // case API_Code::TURNRIGHT:
            //     return $this->AGV->spinRight(90);
            //     break;
            // case API_Code::TURNRIGHT:
            //     return $this->AGV->spinRight(90);
            //     break;
        }
    }

    public function getData($code = API_Code::ALL){
        switch($code){
            case API_Code::ALL:
                return $this->AGV->getData()->toArray();
                break;
            case API_Code::BATTERY:
                return $this->AGV->getBattery()->toArray();
                break;
            case API_Code::POS:
                return $this->AGV->getMapPosition()->toArray();
                break;
            case API_Code::IsLeftUp:
                return $this->AGV->checkIsLeftUp()->toArray();
                break;
            case API_Code::POS:
                return $this->AGV->getMapPosition()->toArray();
                break; 
        }
    }

    public function getDataFormat($data){
        
    }

    public function GoPosition($x, $y, $yaw){
        $Data = $this->AGV->getData()->getConfig();
        //return $Data;
        $dfs = new DFS();
        // return [intval($Data['Attitude']['Code']['4']), intval($Data['Attitude']['Code']['1']), $this->absAngle($Data['Attitude']['Yaw'])/90];
        $dfs->Run(new point(intval($Data['Attitude']['Code']['4']), intval($Data['Attitude']['Code']['1']), $this->compareDFSYaw($this->absAngle($Data['Attitude']['Yaw'])/90), 0));
        $path = $dfs->getPath($x, $y);
        $script = $path->script;
        return $script;
        // return $this->DoScript($script);
    }

    public function DoScript($param = array()){
        return $this->AGV->Script($param);
    }

    public function GoCharge(){
        //if($AGV->getData()->)
    }

    private function absAngle($angle){
        while($angle<0)$angle+=360;
        return $angle%360;
    }

    private function compareDFSYaw($yaw){
        switch($yaw){
            case 0:
                return 1;
                break;
            case 1:
                return 0;
                break;
            case 2:
                return 3;
                break;
            case 3:
                return 2;
                break;
        }
    }

    public function spin($degree, $lockingDisc = true){ // 旋轉 : lockingDisc鎖定圓盤不動
        throw Exception(__FUNCTION__ . "is unactive.");
    }
}