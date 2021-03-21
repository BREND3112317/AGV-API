<?php

namespace BREND\AGV\Controllers;

use BREND\Constants\STATUS;
use BREND\Constants\AGVSTATUS;
use BREND\Constants\API_Code;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Models\AGV;
use BREND\AGV\Algorithms\AGV_DFS;
use BREND\AGV\Algorithms\point;
use BREND\AGV\Exceptions\AGVException;

class AGVController{
    protected $api_url;
    protected $AGV_name;

    private $map = [ [-1,-2, 0, 0,-1,-1,-1],
                     [-1,-1, 0, 0, 0,-2,-2],
                     [-1,-1, 0, 0, 0,-2,-2],
                     [-1,-1, 0, 0, 0, 0,-2],
                     [-1, 0, 0, 0, 0, 0,-2],
                     [-1, 0, 0, 0, 0, 0,-1],
                     [-1, 0, 0, 0, 0, 0,-1],
    ];

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
            case API_Code::ServoOn:
                return $this->AGV->ServoOn();
                break;
            case API_Code::ServoOff:
                return $this->AGV->ServoOff();
                break;
            case API_Code::RUN1000:
                $this->DoNotInPluging();
                return $this->AGV->move(1000);
                break;
            case API_Code::TURNREG:
                $this->DoNotInPluging();
                return $this->AGV->Trans2AbsYawSpin($param['Yaw']);
                break;
            case API_Code::TURNLEFT:
                $this->DoNotInPluging();
                return $this->AGV->spinLeft(90);
                break;
            case API_Code::TURNRIGHT:
                $this->DoNotInPluging();
                return $this->AGV->spinRight(90);
                break;
            case API_Code::TURNBACK:
                $this->DoNotInPluging();
                return $this->AGV->spinRight(180);
                break;
            case API_Code::SHELFUP:
                $this->DoNotInPluging();
                return $this->AGV->shelfup();
                break;
            case API_Code::SHELFDOWN:
                $this->DoNotInPluging();
                return $this->AGV->shelfdown();
                break;
            case API_Code::PLUGIN:
                $this->DoNotInPluging();
                return $this->GoChargeing();
                break;
            case API_Code::PLUGOUT:
                return $this->DoNotInPluging();
                break;
            case API_Code::DoScript:
                return $this->GoPosition($param['code'], $param['yaw']);
                break;
            case API_Code::GoChargeing:
                return $this->GoChargeing();
                break;
            // case API_Code::TURNRIGHT:
            //     return $this->AGV->spinRight(90);
            //     break;
        }
    }

    public function getData($code = API_Code::ALL){
        switch($code){
            case API_Code::MAIN:
                $Data = $this->AGV->getPrepareData();
                $Data['Priview'] = $this->getPreviewPath();
                return $Data;
                break;
            case API_Code::ALL:
                $Data = $this->AGV->getData()->toArray();
                //$Data['Preview'] = $this->getPreviewPath();
                return $Data;
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
            case API_Code::PreviewPath:
                return $this->getPreviewPath();
                break; 
            case API_Code::PathScript:
                return $this->getPreviewPath();
                break; 
        }
    }

    

    public function setMap($map){

    }

    public function getPreviewPath(){
        $Data = $this->AGV->getData()->getConfig();
        $dfs = new AGV_DFS($this->map);
        $dfs->setStartPoint($Data['Attitude']['Code'], $Data['Attitude']['Yaw']);
        $dfs->Run();
        $paths = $dfs->getAGVPreviewPath();
        return $paths;
    }

    public function GoPosition($code, $yaw){
        $this->DoNotInPluging();
        $Data = $this->AGV->getData()->getConfig();
        $dfs = new AGV_DFS($this->map);
        $dfs->setStartPoint($Data['Attitude']['Code'], $Data['Attitude']['Yaw']);
        $dfs->Run();
        $path = $dfs->getCodePath($code);
        // $dfs->showPreviewPath($code);
        $script = $path->script;
        // return $path;
        return $this->DoScript($script);
    }

    public function GoChargeing(){
        $this->DoNotInPluging();
        $Data = $this->AGV->getData()->getConfig();
        $dfs = new AGV_DFS($this->map);
        $dfs->setStartPoint($Data['Attitude']['Code'], $Data['Attitude']['Yaw']);
        // $dfs->setStartPoint("030050", 180);

        $dfs->Run();
        $path = $dfs->getCodePath("060050");
        //var_dump($path);
        $scripts = $path->script;
        // echo $this->translateScript(30230, 700);
        $this->pushScript($this->translateScript($this->REGTurnCmd($dfs->compareAGVYaw($path->yaw), 90)), $scripts);
        $this->pushScript($this->translateScript(30230, 700), $scripts);
        // return $scripts;
        return $this->DoScript($scripts);
    }

    public function pushScript($script, &$scripts = array()){
        // echo $script;
        if($script != null){
            array_push($scripts, $script);
        }
        return $scripts;
    }    

    public function DoScript($param = array()){
        return $this->AGV->Script($param);
    }

    public function DoNotInPluging($Data = null){
        if($Data == null){
            $Data = $this->AGV->getData()->getStatus();
        }
        if($Data['IsChargeing'] == true){
            // return false;
            $this->AGV->PlugOut();
        }
    }

    public function REGTurnCmd($yaw, $yaw_after){
        $_yaw = $this->AGV->REGAngle($yaw_after-$yaw);
        switch($_yaw/90){
            case 1:
                return 30208;
                break;
            case 2:
                return 30209;
                break;
            case -1:
                return 30210;
                break;
            case -2:
                return 30211;
                break;
        }
        return null;
    }

    public function translateScript($cmd, $param = null){
        switch($cmd){
            case 30108:
                return '50';
                break;
            case 30109:
                return '60';
                break;
            case 30110:
                return '70';
                break;
            case 30111:
                return '80';
                break;
            case 30208:
                return '150';
                break;
            case 30209:
                return '160';
                break;
            case 30210:
                return '170';
                break;
            case 30211:
                return '180';
                break;
            case 30218:
                return '330';
                break;
            case 30219:
                return '340';
                break;
            case 30214:
                return '350';
                break;
            case 30215:
                return '360';
                break;
            case 30216:
                return '370';
                break;
            case 30217:
                return '380';
                break;
            case 30230:
                return (6000+($param > 0 && $param <= 1000 ? $param : 0)).'';
                break;
            case 30231:
                return (7000+($param > 0 && $param <= 1000 ? $param : 0)).'';
                break;
            case 30112:
                return (10000+($param > 0 && $param < 50000 ? $param/10 : 0)).'';
                break;
        }
        return null;
    }

    public function GoCharge(){
        //if($AGV->getData()->)
    }

    public function spin($degree, $lockingDisc = true){ // 旋轉 : lockingDisc鎖定圓盤不動
        throw Exception(__FUNCTION__ . "is unactive.");
    }
}