<?php

error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT']."/rb/backend/rest/api.php");

class ImportServiceImpl
{
    public $log;
    public $youtubeService;

    public function __construct() {
        $this->log = new LogServiceImpl();
    }

    //Generic import
    public function importg($turnierID){
        //Anmeldungen laden
        $jsonurl = "http://www.beachcrew.at/LogIT_test/rest/ExportService/exportG?turnierID=".$turnierID;
        $json = file_get_contents($jsonurl,0,null,null);
        $json_output = json_decode($json);

        try
        {
            //DB-Transaktion starten
            R::begin();

            //Lokale DB vorbereiten
            R::nuke();
            R::wipe("turnier");
            R::wipe("team");
            R::wipe("bewerb");

            //Turnier erstellen
            $turnierJson = $json_output->turnier;
            $turnier = R::dispense("turnier");
            $turnier->name = $turnierJson->turniername;
            $turnier->turniermail = $turnierJson->turniermail;
            $turnier->nennstart = $turnierJson->nennstart;
            $turnier->nennschluss = $turnierJson->nennschluss;
            $turnier->teilnehmerAlias = $turnierJson->teilnehmerAlias;
            $turnier->teamAlias = $turnierJson->teamAlias;
            R::store($turnier);

            //Bewerbe erstellen
            $bewerbe = array();
            foreach($json_output->bewerbe as $bewerbArray){
                $Bewerb = R::dispense("bewerb");
                $Bewerb->turnier = $turnier;
                $Bewerb->name = $bewerbArray->bewerbname;
                $Bewerb->onlineid = $bewerbArray->id;
                $bewerbe[$Bewerb->onlineid] = $Bewerb;
                R::store($Bewerb);
            }

            //Iteration über alle Anmeldungen
            foreach($json_output->anmeldungen as $anmeldung)
            {
                //Basic Team Stuff
                $Team = R::dispense("team");
                $Team->onlineid = $anmeldung->id;
                $Team->bewerb = $bewerbe[$anmeldung->bewerb];
                $Team->angemeldet = $anmeldung->angemeldet;

                //Bezahlung
                $bezahlungJson = $anmeldung->bezahlung;
                $Team->nenngeldGesamt = $bezahlungJson->kosten;
                $Team->bezahltBetrag = (!empty($bezahlungJson->bezahlt))?$bezahlungJson->bezahlt:0;
          //      $Team->bezahltBetrag = $bezahlungJson->bezahltBetrag;

                //Konstanten
                $Team->bezahltVorort = 0;
                $Team->anwesend = false;
                $Team->importiert = R::isoDateTime();

                //Speichern
                R::store($Team);

                //Spieler
                $spielerJson = $anmeldung->spieler;
                foreach($spielerJson as $spieler){

                    //Spieler
                    $sp = R::dispense("spieler");
                    $sp->team = $Team;
                    $sp->vorname = $spieler->vorname;
                    $sp->nachname = $spieler->nachname;
                    $sp->nenngeld = $spieler->nenngeld;
                    $sp->kaution = $spieler->kaution;
                    R::store($sp);

                    //Spielerinfo
                    $spielerinfoJson = $spieler->info;
                    foreach($spielerinfoJson as $info){
                        $spielerinfo = R::dispense("spielerinfo");
                        $spielerinfo->spieler = $sp;
                        $spielerinfo->titel = $info->titel;
                        $spielerinfo->wert = $info->wert;
                        $spielerinfo->anmeldefeld = $info->anmeldefeld;
                        $spielerinfo->feldart = $info->feldart;
                        R::store($spielerinfo);
                    }
                }

                //Teaminfo
                $infos_json = $anmeldung->info;
                foreach($infos_json as $info){
                    $teaminfo = R::dispense("teaminfo");
                    $teaminfo->team = $Team;
                    $teaminfo->titel = $info->titel;
                    $teaminfo->wert = $info->wert;
                    $teaminfo->anmeldefeld = $info->anmeldefeld;
                    $teaminfo->feldart = $info->feldart;
                    R::store($teaminfo);
                }

                $Team->generateSpielerString();

                //Log
                $this->log->logImport($Team);
            }

            //Commit DB Transaktion
            R::commit();

        }catch (Exception $e){
            R::rollback();
            $result = array("message"=>"Während dem importieren der Anmeldungen ist leider folgender Fehler aufgetretten: ".$e->getMessage(),"error"=>$e);
            $this->response($this->json($result), 400);
        }
    }

    public function import($turnierID){

        //Anmeldungen laden
        $jsonurl = "http://www.beachcrew.at/LogIT_test/rest/ExportService/export?turnierID=".$turnierID;
        $json = file_get_contents($jsonurl,0,null,null);
        $json_output = json_decode($json);

        try
        {
            //DB-Transaktion starten
            R::begin();

            //Lokale DB vorbereiten
            R::nuke();
            R::wipe("team");
            R::wipe("bewerb");

            //Bewerbe erstellen
            $bewerbe = array();
            foreach($json_output->bewerbe as $bewerbArray){
                $Bewerb = R::dispense("bewerb");
                $Bewerb->name = $bewerbArray->bewerbname;
                $Bewerb->onlineid = $bewerbArray->id;
                $bewerbe[$Bewerb->onlineid] = $Bewerb;
                R::store($Bewerb);
            }

            //Iteration über alle Anmeldungen
            foreach($json_output->anmeldungen as $anmeldung)
            {
                //Team Stuff
                $Team = R::dispense("team");
                $Team->onlineid = $anmeldung->id;
                $Team->bewerb = $bewerbe[$anmeldung->bewerb];
                $Team->angemeldet = $anmeldung->angemeldet;
                $Team->teamname = $anmeldung->Teamname;
                $Team->email = $anmeldung->EMail;
                $Team->handynummer = $anmeldung->Handynummer;
                $Team->herkunft = $anmeldung->Herkunft;

                //Spieler Stuff
                $Team->spieler1 = $anmeldung->spieler1;
                $Team->spieler2 = $anmeldung->spieler2;
                $Team->spieler1ZweiterBewerb = $anmeldung->spieler1Infos->Zweiter_Bewerb == "1";
                $Team->spieler2ZweiterBewerb = $anmeldung->spieler2Infos->Zweiter_Bewerb == "1";

                //Nenngeld stuff
                $Team->nenngeld1 = $anmeldung->nenngeld1;
                $Team->nenngeld2 = $anmeldung->nenngeld2;
                $Team->kaution1 = $anmeldung->kaution1;
                $Team->kaution2 = $anmeldung->kaution2;
                $Team->nenngeldGesamt = $anmeldung->kosten;

                //Bezahlt stuff
                $Team->bezahltDatum = $anmeldung->bezahlt;
                $Team->betragBezahlt = 10;
                $Team->bezahltEingetragenAm = R::isoDateTime();

                //Konstanten
                $Team->anwesend = false;
                $Team->importiert = R::isoDateTime();

                //Speichern
                R::store($Team);

                //Log
                $this->log->logImport($Team);
            }

            //Commit DB Transaktion
            R::commit();

        }catch (Exception $e){
            R::rollback();
            $result = array("message"=>"Während dem importieren der Anmeldungen ist leider folgender Fehler aufgetretten: ".$e->getMessage(),"error"=>$e);
            $this->response($this->json($result), 400);
        }
    }
}
?>