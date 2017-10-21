<?php

require_once($_SERVER['DOCUMENT_ROOT']."/selos/backend/api/api.php");

class GruppeService extends GENERIC_API
{
    protected $TABLE_NAME = "gruppe";
    protected $PERMISSIONS = array();

    function __construct(){
        parent::__construct();
    }

    public function drucken(){
        $printManager = new printManagerImpl();

        $params = json_decode($this->_request);
        $printRaster = @$params->raster;
        $printGames = @$params->spiele;
        $printBackgroundRaster = @$params->backgroundRaster;
        $printBackgroundSpiele = @$params->backgroundSpiele;
        $rasterFormat = @$params->format;
        $rasterGroesse = @$params->groesse;
        $info = @$params->info;
        $neuesDesign = @$params->neuesDesign;

        $teams = @$params->teams;

        $bewerb = @$params->bewerb;
        $gruppenname = @$params->gruppenname;

        $offsetX = 0;
        $offsetY = 0;

        if($printGames){
            $printManager->printGames($teams,$bewerb,$gruppenname,$info,$printBackgroundSpiele,$offsetX,$offsetY,true);
        }

        if($printRaster){

            if($neuesDesign){

                $printManager->printRasterNeu($teams,$bewerb,$gruppenname,$info,$printBackgroundRaster,$offsetX,$offsetY,true);

               // $printManager->printRasterNeu($teams,$bewerb,$gruppenname,$rasterFormat,$printBackgroundRaster,$rasterGroesse,$offsetX,$offsetY,true);
            }

            $printManager->printRaster($teams,$bewerb,$gruppenname,$rasterFormat,$printBackgroundRaster,$rasterGroesse,$offsetX,$offsetY,true);
        }
    }


}
$GruppeService = new GruppeService;