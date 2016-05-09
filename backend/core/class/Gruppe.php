<?php
class Model_Gruppe extends RedBean_SimpleModel{

    public function drucken($service){
        $printManager = new printManagerImpl();

        $params = json_decode($service->_request);
        $printRaster = @$params->raster;
        $printGames = @$params->spiele;
        $printBackgroundRaster = @$params->backgroundRaster;
        $printBackgroundSpiele = @$params->backgroundSpiele;
        $rasterFormat = @$params->format;
        $rasterGroesse = @$params->groesse;
        $info = @$params->info;

        $offsetX = 0;
        $offsetY = 0;

        if($printGames){
            $printManager->printGames($this->ownTeam,$this->bewerb->name,$this->name,$info,$printBackgroundSpiele,$offsetX,$offsetY);
        }

        if($printRaster){
            $printManager->printRaster($this->ownTeam,$this->bewerb->name,$this->name,$rasterFormat,$printBackgroundRaster,$rasterGroesse,$offsetX,$offsetY);
        }
    }

    public function getTeamNames(){
        $names = array();
        foreach($this->ownTeam as $team){
            $names[] = $team->teamname;
        }
        return $names;
    }
}
?>