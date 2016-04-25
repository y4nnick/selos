<?php

require_once($_SERVER['DOCUMENT_ROOT']."/rb/backend/api/api.php");

class GruppeService extends GENERIC_API
{
    protected $TABLE_NAME = "gruppe";
    protected $PERMISSIONS = array();

    function __construct(){
        parent::__construct();
    }


}
$GruppeService = new GruppeService;