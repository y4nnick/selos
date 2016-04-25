<?php
class Model_Bewerb extends RedBean_SimpleModel{

    public function auslosen($service){
        $aufteilung = $_GET["aufteilung"];
        Val::assert(empty($aufteilung),"Eine Aufteilung muss übergeben werden");

        $gruppenSize = explode(",",$aufteilung);

        //Check if count is correct
        $sum = 0;
        for($i = 0; $i < count($gruppenSize); $i++){
            $groesse = $gruppenSize[$i];
            $sum += $groesse;
        }

        if($sum != count($this->ownTeam)){
            throw new Exception("Anzahl der Teams in diesem Bewerb stimmen nicht mit der Anzahl der Teams in den Gruppen überein");
        }

        //Gruppennamen
        $names = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

        $teams = $this->ownTeam;
        shuffle($teams);

        try{
            R::begin();

            //Alte Auslosung löschen
            $this->xownGruppe = array();

            for($i = 0; $i < count($gruppenSize); $i++){
                $groesse = $gruppenSize[$i];

                //Gruppe erstellen
                $gruppe = R::dispense("gruppe");
                $gruppe->name = $names[$i];
                $gruppe->groesse = $groesse;
                $gruppe->bewerb = $this;
                R::store($gruppe);

                //Teams zur Gruppe zuordnen
                for($x = 0;$x < $groesse;$x++){
                    $nextTeam = array_pop($teams);
                    $nextTeam->gruppe = $gruppe;
                    R::store($nextTeam);
                }
            }

            $this->ausgelost = true;
            R::store($this);

            R::commit();
        }catch(Exception $e){
            R::rollback();
            throw new Exception("Fehler während dem Auslosen: ".$e->getMessage());
        }
    }

}
?>