<?php

namespace BREND\AGV\Controllers;

use BREND\AGV\Models\AGV_request;
use BREND\AGV\Models\AGV_response;
use BREND\AGV\Exceptions\AGVException;

class AGVController{
    protected $api_url;
    protected $AGV_name;
    protected $_Status = null;

    public function __construct($name, $url = null){
        $this->AGV_name = $name;
        $this->api_url = ($url == null ? AGV_request::$url : $url);
    }

    public function getStatus(){
        $httpResponse = AGV_request::POST($this->AGV_name, "500", array(), $this->api_url);
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }

    public function checkError(){
        if(($statusCode = $this->_Status->getStatusCode()) !== 0){
            throw AGVException("Status Error Code: " . $statusCode);
        }
        return false;
    }

    public function getBattery(){
        $this->getStatus();
        return $this->_Status->getBattery();
    }

    public function getMapPosition(){
        $this->getStatus();
        return $this->_Status->getAttitude();
        return ['Code' => $httpResponse['config']['Attitude']['Code'], 'Yaw' => $httpResponse['config']['Attitude']['Yaw']];
    }

    public function getNowPosition(){
        $this->getStatus();
        return [
            'X' => $httpResponse['config']['Pos']['X'], 
            'Y' => $httpResponse['config']['Pos']['Y'],
            'A' => $httpResponse['config']['Pos']['A']
        ];
    }

    public function move($gap){
        $httpResponse = AGV_request::POST($this->AGV_name, "30112", array($gap), $this->api_url);
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }

    public function rise(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30218", array(), $this->api_url);
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }

    public function fall(){
        $httpResponse = AGV_request::POST($this->AGV_name, "30219", array(), $this->api_url);
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }

    private function absAngle($angle){
        while($angle<0)$angle+=360;
        return $angle%360;
    }
    public function spin($degree, $lockingDisc = true){ // 旋轉 : lockingDisc鎖定圓盤不動
        throw Exception(__FUNCTION__ . "is unactive.");
    }
    public function spinLeft($degree, $lockingDisc = true){
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
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }

    public function spinRight($degree, $lockingDisc = true){
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
        $this->_Status = new AGV_response($httpResponse);
        $this->checkError();
        return $this->_Status;
    }
}