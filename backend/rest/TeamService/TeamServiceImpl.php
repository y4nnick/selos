<?php

error_reporting(E_ERROR);
require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/rest/api.php");

class TeamServiceImpl
{
    public $log;
    public $youtubeService;

    public function __construct() {
        $this->log = new LogServiceImpl();
    }

    public function update($team){
        R::store($team);
        return $team;
    }

    public function getAnwesenheiten(){
        error_reporting(E_ALL);

        $Bewerbe = R::findAll("bewerb");

        $allGesamt = 0;
        $allAnwesend = 0;

        $result = array();
        $result['bewerbe'] = array();

        foreach($Bewerbe as $bewerb){

            $gesamt = $bewerb->countOwn('team');
            $anwesend = $bewerb->withCondition('anwesend = ?',[true])->countOwn('team');

            $result['bewerbe'][] = array(
                "id"=>$bewerb->id,
                "name"=>$bewerb->name,
                "gesamt"=>$gesamt,
                "anwesend"=>$anwesend,
                "prozent"=>(($gesamt!=0)?(($anwesend/$gesamt)*100):0)
            );

            $allGesamt += $gesamt;
            $allAnwesend += $anwesend;
        }

        $result['gesamt'] = array("gesamt"=>$allGesamt,"anwesend"=>$allAnwesend,"prozent"=>(($allGesamt!=0)?(($allAnwesend/$allGesamt)*100):0));

        return $result;
    }

    public function query(){

        return R::findAll("team","order by onlineid");

        /*if(empty($id) && empty($text)){
            return R::findAll("team","order by onlineid");
        }

        if(!empty($id)){
            return R::find("team","onlineid LIKE ? order by onlineid",["%".$id."%"]);
        }

        //Text search only here
        return R::find("team", "teamname LIKE ? OR spieler1 LIKE ? OR spieler2 LIKE ? order by onlineid",["%".$text."%","%".$text."%","%".$text."%"]);
        */
        //$all =  R::find("team");//, "status_id <> ?", [EID('status:deleted')]);
    }


    public function getLogs($pitchID){
        return R::find("log","pitch_id = ? ORDER BY date",[$pitchID]);
    }

}
?>