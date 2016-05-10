<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/api/api.php");

class GemeinschaftService extends GENERIC_API
{
    protected $TABLE_NAME = "gemeinschaft";
    protected $PERMISSIONS = array();

    function __construct(){
        parent::__construct();
    }

    public function afterGetList($data){
        foreach($data as $item){
            foreach($item->ownTeam as $team){
                $team->setDisplayFields(true);
            }
        }
    }

    public function afterGet($item){
        foreach($item->ownTeam as $team){
            $team->setDisplayFields(true);
        }
    }

}
$GemeinschaftService = new GemeinschaftService ;