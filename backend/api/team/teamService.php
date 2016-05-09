<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/api/api.php");

class TeamService extends GENERIC_API
{
    protected $TABLE_NAME = "team";
    protected $PERMISSIONS = array();


    function __construct(){
        parent::__construct();
    }

    public function afterGetList($data){
        foreach($data as $item){
            $item->setDisplayFields(true);
        }
    }

    public function afterGet($item){
        $item->setDisplayFields(false);
    }

    public function afterUpdate($entity){
      //  $entity->setDisplayFields(false);
        $entity->generateSpielerString();
        $entity->setDisplayFields(false);
    }


    public function zahlungenVorOrt(){
        $Teams = R::findAll("team");

        $bezahlt = 0;
        $bezahltCount = 0;
        foreach($Teams as $team){
            if($team->bezahltVorort != 0){
                $bezahltCount++;
                $bezahlt += $team->bezahltVorort;
            }
        }

        $result = array();
        $result["bezahlt"] = $bezahlt;
        $result["bezahltCount"] = $bezahltCount;
        return $result;
    }

    public function anwesenheit(){
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
}
$TeamService = new TeamService;