<?php
class Model_Team extends RedBean_SimpleModel{

    /**
     * Setzt die wichtigesten Teamfelder direkt im Team und nicht nur in den teaminfos
     */
    public function setDisplayFields($list){

        //Diese Felder sollen direkt zum turnier gespeichert werden
        //TODO make configable
        $displayFields = array("Teamname","Herkunft","Handynummer");

        $fields = array();
        foreach($this->ownTeaminfo as $info){
            if(in_array($info->titel,$displayFields)){
                $display = array();
                $display["titel"] = $info->titel;
                $display["wert"] = $info->wert;
                $fields[] = $display;

                $fields[$info->titel] = $info->wert;
            }
        }

        if($list)$this->ownTeaminfo = null;
        $this->display = $fields;
    }



    /**
     * Generiert und speichert den Spieler String
     */
    public function generateSpielerString(){

        $str = "";

        $x = 0;
        foreach ($this->ownSpieler as $spieler) {

            $str.= $spieler->vorname ." ".$spieler->nachname;

            if(count($this->ownSpieler) != ++$x){
                $str.=" / ";
            }
        }

        $this->spieler = $str;
        R::store($this);
    }
}
?>