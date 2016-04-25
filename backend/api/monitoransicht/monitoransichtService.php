<?php

require_once($_SERVER['DOCUMENT_ROOT']."/rb/backend/api/api.php");

class MonitoransichtService extends GENERIC_API
{
    protected $TABLE_NAME = "monitoransicht";
    protected $PERMISSIONS = array();

    function __construct(){
        parent::__construct();
    }

}
$monitoransichtService = new MonitoransichtService;