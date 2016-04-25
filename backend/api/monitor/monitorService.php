<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/api/api.php");

class MonitorService extends GENERIC_API
{
    protected $TABLE_NAME = "monitor";
    protected $PERMISSIONS = array();

    function __construct(){
        parent::__construct();
    }

    public function getByNumber(){

        $number = Val::checkForNotEmpty($this,"number");

        $monitor = R::findOne($this->TABLE_NAME,"number = ?",array($number));

        if($monitor == null || $monitor->id == 0){
            return array("result"=>"No monitor with number $number found","success"=>false);
        }

        return $monitor;
    }
}
$monitorService = new MonitorService;