<?php

namespace BREND\AGV\Models;

use BREND\Constants\STATUS;
use BREND\Constants\AGVSTATUS;
use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Exceptions\AGVException;

class AGV{
    protected $api_url;
    protected $AGV_name;
    protected $_Data = null;

    public function __construct($name, $url = null){
        $this->AGV_name = $name;
        $this->api_url = ($url == null ? AGV_request::$url : $url);
    }

    public function getData(){
        $httpResponse = AGV_request::POST($this->AGV_name, "500", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function checkError(){
        if(($statusCode = $this->_Data->getStatusCode()) !== AGVSTATUS::SUCCESS){
            throw new AGVException($this->_Data->getConfig(), $statusCode);
        }
        return false;
    }

    public function getPrepareData(){
        /*
            Battery string  電池電量
            IsProgress bool  是否在執行
            Status string  API狀態碼
            AGV_x string  電池電量
            Battery string  電池電量
            Battery string  電池電量
            Battery string  電池電量
            Battery string  電池電量
            Battery string  電池電量
        */
        $data = $this->getData()->toArray();

        $prepareData['Battery'] = $data['Config']['Battery'];
        $prepareData['AgvLogIndex']['IsProgress'] = $data['Config']['AgvLogIndex']['IsProgress'];
        $prepareData['Attitude']['Code'] = $data['Config']['Attitude']['Code'];
        $prepareData['Attitude']['Yaw'] = $data['Config']['Attitude']['Yaw'];
        $prepareData['Shelves']['Yaw'] = $data['Config']['Shelves']['Yaw'];
        return $prepareData;
    }

    public function getBattery(){
        $this->getData();
        $this->_Data->getBattery();
        return $this->_Data;
    }

    public function getStatus(){
        $this->getData();
        $this->_Data->getStatus();
        return $this->_Data;
        // return ['StatusCode' => $this->_Data->getStatusCode(), 'Battery' => $this->_Data->getStatus()];
    }

    public function getMapPosition(){
        $this->getData();
        $this->_Data->getAttitude();
        return $this->_Data;
        // return ['StatusCode' => $this->_Data->getStatusCode(), 'Attitude' => $this->_Data->getAttitude()];
    }

    public function getPosition(){
        $this->getData();
        $this->_Data->getPos();
        return $this->_Data;
        // return ['StatusCode' => $this->_Data->getStatusCode(), 'Pos' => $this->_Data->getPos()];
    }

    public function getAgvLogIndex(){
        $this->getData();
        $this->_Data->getAgvLogIndex();
        return $this->_Data;
        // return ['StatusCode' => $this->_Data->getStatusCode(), 'AgvLogIndex' => $this->_Data->getAgvLogIndex()];
    }

    public function getRunPara(){
        $this->getData();
        $this->_Data->getRunPara();
        return $this->_Data;
        // return ['StatusCode' => $this->_Data->getStatusCode(), 'RunPara' => $this->_Data->getRunPara()];
    }

    public function Script($param = array()){
        $httpResponse = AGV_request::POST($this->AGV_name, "30121", $param, $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function DirectSTOP(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30310", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function ScriptOVER(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30314", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function ScriptContinue(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30313", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function ScriptSTOP(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30312", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function ServoOn(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30100", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function ServoOff(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30101", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function move($gap){
        $httpResponse = AGV_request::POST($this->AGV_name, "30112", array($gap), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function shelfup(){
        if($this->checkIsLeftUp()){
            return $this->getData();
        }
        $httpResponse = AGV_request::POST($this->AGV_name, "30218", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function shelfdown(){
        if($this->checkIsLeftUp() == false){
            return $this->getData();
        }
        $httpResponse = AGV_request::POST($this->AGV_name, "30219", array(), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function PlugIn($gap=700, $enforce = false){
        // if($this->checkPosition("060050")){
        //     throw new AGVException("Position Code is not in the default Code", UNKNOWN_ERROR);
        // }
        // if($this->checkYaw(90)==false){
        //     throw new AGVException("Position Yaw is not in the default Yaw", UNKNOWN_ERROR);
        // }
        // if($this->checkIsChargeing()){
        //     throw new AGVException("isChargeing", UNKNOWN_ERROR);
        // }
        $httpResponse = AGV_request::POST($this->AGV_name, "30230", array($gap), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }
    
    public function PlugOut($gap=700, $enforce = false){
        // if($this->checkIsChargeing() == false){
        //     throw new AGVException("AGV is not Chargeing", UNKNOWN_ERROR);
        // }
        $httpResponse = AGV_request::POST($this->AGV_name, "30231", array($gap), $this->api_url);
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function Trans2AbsYawSpin($yaw, $lockingDisc = true){
        $this->getData();
        $attitude = $this->_Data->getAttitude();
        // $attitude['Yaw'] = 270;
        $_yaw = $this->REGAngle($yaw-$attitude['Yaw']);
        if($_yaw>0){//右轉
            echo "右轉: " . $_yaw;
            return $this->spinRight($_yaw, $lockingDisc);
        }else if($_yaw<0){//左轉
            echo "左轉: " . $_yaw;
            return $this->spinLeft($_yaw, $lockingDisc);
        }
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function quickSpin($degree, $lockingDisc = true){ // 旋轉 : lockingDisc鎖定圓盤不動
        $_yaw = $this->REGAngle($degree);
        if($_yaw>0){//右轉
            echo "右轉: " . $_yaw;
            return $this->spinRight($_yaw, $lockingDisc);
        }else if($_yaw<0){//左轉
            echo "左轉: " . $_yaw;
            return $this->spinLeft($_yaw, $lockingDisc);
        }
        return $this->getData();
    }

    public function spinLeft($spin, $lockingDisc = true){
        switch($this->absAngle($spin)/90){
            case 1:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30210" : "30110", array(), $this->api_url);
                break;
            case 2:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30211" : "30111", array(), $this->api_url);
                break;
            case 3:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30211" : "30111", array(), $this->api_url);
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30210" : "30110", array(), $this->api_url);
                break;
        }
        return $this->getData();
    }

    public function spinRight($spin, $lockingDisc = true){
        switch($this->absAngle($spin)/90){
            case 1:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30208" : "30108", array(), $this->api_url);
                break;
            case 2:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30209" : "30109", array(), $this->api_url);
                break;
            case 3:
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30209" : "30109", array(), $this->api_url);
                $httpResponse = AGV_request::POST($this->AGV_name, $lockingDisc ? "30208" : "30108", array(), $this->api_url);
                break;
        }
        $this->_Data = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Data;
    }

    public function REGAngle($angle){
        while($angle<-180)$angle+=360;
        while($angle>180)$angle-=360;
        $angle = round($angle/90)*90;
        return $angle;
    }

    private function absAngle($angle){
        while($angle<0)$angle+=360;
        $angle = round($angle/90)*90;
        return $angle%360;
    }

    public function checkYaw($yaw){
        $this->getData();
        $Attitude = $this->_Data->getAttitude();
        return $yaw == $this->absAngle($Attitude['Yaw']);
    }

    public function checkPosition($code){
        $this->getData();
        $Attitude = $this->_Data->getAttitude();
        return $code == $Attitude['Code'];
    }

    public function checkIsChargeing(){
        $this->getData();
        $status = $this->_Data->getStatus();
        return $status['IsChargeing'];
    }

    public function checkIsLeftUp(){
        $this->getData();
        $status = $this->_Data->getStatus();
        return $status['IsLiftUp'];
    }
}