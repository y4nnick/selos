<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/api/api.php");

class BewerbService extends GENERIC_API
{
    protected $TABLE_NAME = "bewerb";
    protected $PERMISSIONS = array();


    function __construct(){
        parent::__construct();
    }

    function afterGet($bewerb){

        //Anwesende Zählen
        $gesamt = count($bewerb->ownTeam);
        $anwesend = 0;
        foreach($bewerb->ownTeam as $team){
            if($team->anwesend){
                $anwesend++;
            }
        }
        $bewerb->teams = $gesamt;
        $bewerb->teams_anwesend = $anwesend;

        //Team Infoladen
        foreach($bewerb->ownGruppe as $gruppe){
            foreach($gruppe->ownTeam as $team){
                $team->setDisplayFields(false);
            }
        }
    }

    function afterUpdate($bewerb){
        //Team Infoladen
        foreach($bewerb->ownGruppe as $gruppe){
            foreach($gruppe->ownTeam as $team){
                $team->setDisplayFields(false);
            }
        }
    }

}
$BewerbService = new BewerbService;