<?php
define('FPDF_FONTPATH','font/');
require('fpdf/fpdf.php');

class ownPDF extends FPDF{

    var $angle = 0;
    var $offsetX = 0;
    var $offsetY = 0;

    function drawGame($team1, $team2, $bewerb, $gruppe, $info,$printBackground,$offsetX,$offsetY, $fromCustom = false){

        $printBorder = false;

        $offsetY = 5;

        if(!$fromCustom){
            $team1->setDisplayFields(false);
            $team2->setDisplayFields(false);
        }

        if($printBackground){
            $corePath = realpath(dirname(__FILE__));
            $this->Image($corePath.'/backgrounds/Spielzettel.jpg',0,0+$offsetY,210);
        }

        $hoeheInfoFelder = 12;
        $abstandVonObenInfofelder = 5;

        $this->SetFont('Arial','B',14);
        $this->SetY($abstandVonObenInfofelder+$offsetY);

        //Bewerb
        $abstandBewerbFeld = 8;
        $breiteBewerbFeld = 69;
        $this->SetX($abstandBewerbFeld);
        $this->Cell($breiteBewerbFeld,$hoeheInfoFelder,utf8_decode($bewerb),$printBorder,0,"C");


        //Gruppe
        $abstandGruppeFeld = 81;
        $breiteGruppeFeld = 38;
        $this->SetX($abstandGruppeFeld);
        $this->Cell($breiteGruppeFeld,$hoeheInfoFelder,utf8_decode($gruppe),$printBorder,0,"C");


        //Info
        $this->SetFont('Arial','B',10);
        $abstandInfoFeld = 123;
        $breiteInfoFeld = 79;
        $this->SetY($abstandVonObenInfofelder + 3+$offsetY);
        $this->SetX($abstandInfoFeld);
        $this->MultiCell($breiteInfoFeld,5,utf8_decode($info),$printBorder,"C");


        //
        // Team
        //

        $hoeheTeamFelder = 5;
        //$abstandVonObenTeamfelder = 26; Für längerere Teamnamen
        $abstandVonObenTeamfelder = 29 +$offsetY;

        $this->SetY($abstandVonObenTeamfelder);
        $this->SetFont('Arial','B',14);

        //Team 1
        $abstandTeam1Feld = 17;
        $breiteTeam1Feld = 79;
        $this->SetX($abstandTeam1Feld);
        $this->MultiCell($breiteTeam1Feld,$hoeheTeamFelder,utf8_decode($this->getTeamname($team1,$fromCustom)),$printBorder,"C");

        //Team 2
        $abstandTeam2Feld = 112;
        $breiteTeam2Feld = 79;
        $this->SetY($abstandVonObenTeamfelder);
        $this->SetX($abstandTeam2Feld);
        $this->MultiCell($breiteTeam2Feld,$hoeheTeamFelder,utf8_decode($this->getTeamname($team2,$fromCustom)),$printBorder,"C");


        //
        // Spieler
        //
        $this->SetFont('Arial','B',8);
        $hoeheSpielerFelder = 3;
        $abstandVonObenSpieler = 38;
        $this->SetY($abstandVonObenSpieler+$offsetY);

        //Team 1
        $this->SetX($abstandTeam1Feld);
        $this->MultiCell($breiteTeam1Feld,$hoeheSpielerFelder,utf8_decode($this->getSpieler($team1,$fromCustom)),$printBorder,"C");

        //Team 1
        $this->SetY($abstandVonObenSpieler+$offsetY);
        $this->SetX($abstandTeam2Feld);
        $this->MultiCell($breiteTeam2Feld,$hoeheSpielerFelder,utf8_decode($this->getSpieler($team2,$fromCustom)),$printBorder,"C");
    }

    function draw6erRasterA3($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom = false){
        $printBorder = false;

        if($printBackground){
            $corePath = realpath(dirname(__FILE__));
            $this->Image($corePath.'/backgrounds/6_Gruppe_A3.jpg',0,0,420);
        }

        //Bewerb
        $this->SetFont('Arial','B',35);
        $this->SetY(32);
        $this->SetX(80);
        $this->MultiCell(180,10,utf8_decode($bewerb),$printBorder,"L");

        //Gruppe
        $this->SetFont('Arial','B',35);
        $this->SetY((strlen($gruppe)>5)?22:32);
        $this->SetX(342);
        $this->MultiCell(70,10,utf8_decode($gruppe),$printBorder,"L");

        $zeilenHoehe = 23.4;
        $zeilenBreite = 80;
        $spaltenHoehe = 73;
        $spaltenBreite = 31;

        $hoeheNamesSpalte = 8;
        $zeilenTextHoehe = 5;

        $abstandOben = 57;
        $abstandLinks = 110;

        $abstandObenZeilen = $abstandOben + $spaltenHoehe;
        $abstandLinksZeilen = $abstandLinks - $zeilenBreite;

        $x = 0;
        foreach($teams as $team){
            $this->SetFont('Arial','B',20);

            $this->SetY($abstandObenZeilen  + $x * $zeilenHoehe + 7);
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");

            $this->SetFont('Arial','B',10);
            $this->SetY($abstandObenZeilen + ($zeilenHoehe-$hoeheNamesSpalte) + $x * $zeilenHoehe );
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$hoeheNamesSpalte,utf8_decode($this->getSpieler($team)),$printBorder,"C");

            $x++;
        }

        $x = 0;
        foreach($teams as $team) {
            $this->SetY($abstandOben + $spaltenHoehe);
            $this->SetX($abstandLinks + $zeilenTextHoehe + $x * $spaltenBreite + 7);

            $this->Rotate(90);
            $this->SetFont('Arial', 'B', 20);

            $this->MultiCell($spaltenHoehe,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");
            $x++;
        }

        $this->Rotate(0);
    }

    function draw5erRasterA3($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom = false){
        $printBorder = false;

        if($printBackground){
            $corePath = realpath(dirname(__FILE__));
            $this->Image($corePath.'/backgrounds/5_Gruppe_A3.jpg',0,0,420);
        }

        //Bewerb
        $this->SetFont('Arial','B',35);
        $this->SetY(22);
        $this->SetX(70);
        $this->MultiCell(180,10,utf8_decode($bewerb),$printBorder,"L");

        //Gruppe
        $this->SetFont('Arial','B',35);
        $this->SetY((strlen($gruppe)>5)?13:22);
        $this->SetX(352);
        $this->MultiCell(70,10,utf8_decode($gruppe),$printBorder,"L");

        $zeilenHoehe = 29;
        $zeilenBreite = 108;
        $spaltenHoehe = 84;
        $spaltenBreite = 29;

        $hoeheNamesSpalte = 8;
        $zeilenTextHoehe = 5;

        $abstandOben = 45;
        $abstandLinks = 141;

        $abstandObenZeilen = $abstandOben + $spaltenHoehe;
        $abstandLinksZeilen = $abstandLinks - $zeilenBreite;

        $x = 0;
        foreach($teams as $team){
            $this->SetFont('Arial','B',22);

            $this->SetY($abstandObenZeilen  + $x * $zeilenHoehe + 10);
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");

            $this->SetFont('Arial','B',12);
            $this->SetY($abstandObenZeilen + ($zeilenHoehe-$hoeheNamesSpalte) + $x * $zeilenHoehe );
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$hoeheNamesSpalte,utf8_decode($this->getSpieler($team)),$printBorder,"C");

            $x++;
        }

        $x = 0;
        foreach($teams as $team) {
            $this->SetY($abstandOben + $spaltenHoehe);
            $this->SetX($abstandLinks + $zeilenTextHoehe + $x * $spaltenBreite + 7);

            $this->Rotate(90);
            $this->SetFont('Arial', 'B', 22);

            $this->MultiCell($spaltenHoehe,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");
            $x++;
        }

        $this->Rotate(0);
    }

    function draw4erRasterA4($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom = false){

        $printBorder = false;

        if($printBackground){
            $corePath = realpath(dirname(__FILE__));
            $this->Image($corePath.'/backgrounds/4_Gruppe_A4.jpg',0,0,297);
        }

        //Bewerb
        $this->SetFont('Arial','B',30);
        $this->SetY(18);
        $this->SetX(80);
        $this->MultiCell(120,10,utf8_decode($bewerb),$printBorder,"L");

        //Gruppe
        $this->SetFont('Arial','B',30);
        $this->SetY((strlen($gruppe)>5)?10:18);
        $this->SetX(250);
        $this->MultiCell(40,10,utf8_decode($gruppe),$printBorder,"L");

        //
        // Teams
        //

        $zeilenHoehe = 21.5;
        $zeilenBreite = 68;
        $spaltenHoehe = 58;
        $spaltenBreite = 26;

        $hoeheNamesSpalte = 3;
        $zeilenTextHoehe = 5;

        $abstandOben = 39;
        $abstandLinks = 97;

        $abstandObenZeilen = $abstandOben + $spaltenHoehe;
        $abstandLinksZeilen = $abstandLinks - $zeilenBreite;

        //Zeilen
        $x = 0;
        foreach($teams as $team){
            if(!$fromCustom) $team->setDisplayFields(false);
            $this->SetFont('Arial','B',16);

            $this->SetY($abstandObenZeilen  + $x * $zeilenHoehe + 7);
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");

            $this->SetFont('Arial','B',8);
            $this->SetY($abstandObenZeilen + ($zeilenHoehe-$hoeheNamesSpalte) + $x * $zeilenHoehe - 4 );
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$hoeheNamesSpalte,utf8_decode($this->getSpieler($team)),$printBorder,"C");

            $x++;
        }

        //Spalten
        $x = 0;
        foreach($teams as $team) {
            $this->SetY($abstandOben + $spaltenHoehe);
            $this->SetX($abstandLinks + $zeilenTextHoehe + $x * $spaltenBreite + 5);

            $this->Rotate(90);
            $this->SetFont('Arial', 'B', 16);

            $this->MultiCell($spaltenHoehe,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");
            $x++;
        }

        $this->Rotate(0);
    }

    function draw4erRasterA4Neu($teams,$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY,$fromCustom = false){

        $printBorder = false;

        if($printBackground){
            $corePath = realpath(dirname(__FILE__));
            $this->Image($corePath.'/backgrounds/4_Gruppe_neu_jpeg.jpg',0,0,297);
        }

        //Bewerb
        $this->SetFont('Arial','B',16);
        $this->SetY(18);
        $this->SetX(22);
        $this->MultiCell(70,10,utf8_decode($bewerb),$printBorder,"C");

        //Gruppe
        $this->SetFont('Arial','B',16);
        $this->SetY(18);
        $this->SetX(96);
        $this->MultiCell(37,10,utf8_decode($gruppe),$printBorder,"C");

        //Info
        $this->SetFont('Arial','B',10);
        $this->SetY(17);
        $this->SetX(138);
        $this->MultiCell(80,5,utf8_decode($info),$printBorder,"C");

        //
        // Teams
        //

        $zeilenHoehe = 20;
        $zeilenBreite = 60;
        $spaltenHoehe = 55;
        $spaltenBreite = 23;

        $hoeheNamesSpalte = 3;
        $zeilenTextHoehe = 5;

        $abstandOben = 46;
        $abstandLinks = 85;

        $abstandObenZeilen = $abstandOben + $spaltenHoehe;
        $abstandLinksZeilen = $abstandLinks - $zeilenBreite;

        //Zeilen
        $x = 0;
        foreach($teams as $team){
            if(!$fromCustom) $team->setDisplayFields(false);
            $this->SetFont('Arial','B',14);

            $this->SetY($abstandObenZeilen  + $x * $zeilenHoehe + 5);
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");

            $this->SetFont('Arial','B',7);
            $this->SetY($abstandObenZeilen + ($zeilenHoehe-$hoeheNamesSpalte) + $x * $zeilenHoehe - 4);
            $this->SetX($abstandLinksZeilen);
            $this->MultiCell($zeilenBreite,$hoeheNamesSpalte,utf8_decode($this->getSpieler($team)),$printBorder,"C");

            $x++;
        }

        //Spalten
        $x = 0;
        foreach($teams as $team) {
            $this->SetY($abstandOben + $spaltenHoehe);
            $this->SetX($abstandLinks + $zeilenTextHoehe + $x * $spaltenBreite + 5);

            $this->Rotate(90);
            $this->SetFont('Arial', 'B', 14);

            $this->MultiCell($spaltenHoehe,$zeilenTextHoehe,utf8_decode($this->getTeamname($team,$fromCustom)),$printBorder,"C");
            $x++;
        }

        $this->Rotate(0);
    }

    private function getSpieler($team,$fromCustom = false){
        if(!$fromCustom){
            return $team->spieler;
        }else{
            return $team->spieler;
        }
    }

    private function getTeamname($team,$fromCustom = false){
        if(!$fromCustom){
            return $team->display["Teamname"];
        }else{
            return $team->name;
        }
    }

    function __construct($orientation, $unit, $size,$offsetX,$offsetY) {
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
        parent::__construct($orientation, $unit, $size);
    }

    function SetY($y, $resety=true){
        parent::setY($y+$this->offsetY,$resety);
    }

    function SetX($x, $resetx=true){
        parent::setX($x+$this->offsetX,$resetx);
    }

    public function Rotate($angle,$x=-1,$y=-1) {

        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)

        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;

            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
}

class printManagerImpl implements printManager{

    private function getRandomString(){
        return substr(md5(rand()), 0, 7);
    }

    public function printRasterNeu($teamsAssozativ,$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY,$fromCustom = false){

        $format = "A4";

        $pdf = new ownPDF("L","mm",$format,1,4);
        $pdf->AliasNbPages();
        $pdf->AddPage("L");

        $teams = array();
        foreach($teamsAssozativ as $t){
            if(!$fromCustom)$t->setDisplayFields(false);
            $teams[] = $t;
        }

        $pdf->draw4erRasterA4Neu($teams,$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY,$fromCustom);

        $corePath = realpath(dirname(__FILE__));
        $pdf->Output($corePath."/print".$format."_Raster/".$format."_Raster_NEU_".preg_replace('/\s+/', '', $bewerb)."_Gruppe".preg_replace('/\s+/', '', $gruppe)."_4erGruppe_".$this->getRandomString().".pdf","F");
    }

    public function printGames($teams,$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY,$fromCustom = false){
        $pdf = new ownPDF("L","mm","A5",$offsetX,$offsetY);
        $pdf->AliasNbPages();

        $spiele = $this->getSpieleFromTeams($teams);

        foreach($spiele as $spiel){
            $pdf->AddPage("L");
            $pdf->drawGame($spiel[0],$spiel[1],$bewerb,$gruppe,$info,$printBackground,$offsetX,$offsetY,$fromCustom);
        }

        $corePath = realpath(dirname(__FILE__));
        $pdf->Output($corePath."/printA5/".preg_replace('/\s+/', '', $bewerb)."_Gruppe".preg_replace('/\s+/', '', $gruppe)."_".count($teams)."erGruppe_".count($spiele)."Spiele_".$this->getRandomString().".pdf","F");
    }

    public function printRaster($teamsAssozativ, $bewerb, $gruppe, $format, $printBackground, $rasterGroesse,$offsetX,$offsetY,$fromCustom = false) {

        $pdf = new ownPDF("L","mm",$format,$offsetX,$offsetY);
        $pdf->AliasNbPages();
        $pdf->AddPage("L");

        $teams = array();
        foreach($teamsAssozativ as $t){
            if(!$fromCustom)$t->setDisplayFields(false);
            $teams[] = $t;
        }

        if($format == "A4"){

            if($rasterGroesse == 4){
                $pdf->draw4erRasterA4($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom);
            }else{
                throw new Exception("Als A4 ist nur ein 4er Raster möglich");
            }

        }else if($format == "A3"){

            if($rasterGroesse == 5){
                $pdf->draw5erRasterA3($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom);
            }else if($rasterGroesse == 6){
                $pdf->draw6erRasterA3($teams,$bewerb,$gruppe,$printBackground,$offsetX,$offsetY,$fromCustom);
            }else{
                throw new Exception("Als A3 ist nur ein 5er oder 6er Raster möglich");
            }
        }

        $corePath = realpath(dirname(__FILE__));
        $pdf->Output($corePath."/print".$format."_Raster/".$format."_Raster_".preg_replace('/\s+/', '', $bewerb)."_Gruppe".preg_replace('/\s+/', '', $gruppe)."_4erGruppe_".$this->getRandomString().".pdf","F");
    }

    private function getSpieleFromTeams($teamsAssozativ){
        $teamCount = count($teamsAssozativ);
        $spiele = array();

        $teams = array();
        foreach($teamsAssozativ as $t){
            $teams[] = $t;
        }

        switch ($teamCount)
        {
            case 2:
                $spiele[] = array($teams[0], $teams[1]);
                break;
            case 3:
                $spiele[] = array($teams[0],$teams[1]);
                $spiele[] = array($teams[1],$teams[2]);
                $spiele[] = array($teams[0],$teams[2]);
                break;
            case 4:
                $spiele[] = array($teams[0], $teams[1]);
                $spiele[] = array($teams[2], $teams[3]);
                $spiele[] = array($teams[2], $teams[0]);
                $spiele[] = array($teams[1], $teams[3]);
                $spiele[] = array($teams[0], $teams[3]);
                $spiele[] = array($teams[1], $teams[2]);
                break;
            case 5:
                $spiele[] = array($teams[0], $teams[1]);
                $spiele[] = array($teams[2], $teams[3]);
                $spiele[] = array($teams[1], $teams[4]);
                $spiele[] = array($teams[0], $teams[2]);
                $spiele[] = array($teams[3], $teams[4]);
                $spiele[] = array($teams[2], $teams[1]);
                $spiele[] = array($teams[0], $teams[3]);
                $spiele[] = array($teams[2], $teams[4]);
                $spiele[] = array($teams[1], $teams[3]);
                $spiele[] = array($teams[0], $teams[4]);
                break;
            case 6:
                $spiele[] = array($teams[0], $teams[1]);
                $spiele[] = array($teams[2], $teams[3]);
                $spiele[] = array($teams[4], $teams[5]);
                $spiele[] = array($teams[1], $teams[2]);
                $spiele[] = array($teams[3], $teams[4]);
                $spiele[] = array($teams[0], $teams[2]);
                $spiele[] = array($teams[1], $teams[3]);
                $spiele[] = array($teams[0], $teams[4]);
                $spiele[] = array($teams[3], $teams[5]);
                $spiele[] = array($teams[1], $teams[4]);
                $spiele[] = array($teams[2], $teams[5]);
                $spiele[] = array($teams[0], $teams[3]);
                $spiele[] = array($teams[1], $teams[5]);
                $spiele[] = array($teams[2], $teams[4]);
                $spiele[] = array($teams[0], $teams[5]);
                break;
            case 7:
                $spiele[] = array($teams[0], $teams[1]);
                $spiele[] = array($teams[2], $teams[3]);
                $spiele[] = array($teams[4], $teams[5]);
                $spiele[] = array($teams[0], $teams[6]);
                $spiele[] = array($teams[1], $teams[2]);
                $spiele[] = array($teams[3], $teams[4]);
                $spiele[] = array($teams[5], $teams[6]);
                $spiele[] = array($teams[0], $teams[2]);
                $spiele[] = array($teams[1], $teams[3]);
                $spiele[] = array($teams[2], $teams[4]);
                $spiele[] = array($teams[3], $teams[5]);
                $spiele[] = array($teams[4], $teams[6]);
                $spiele[] = array($teams[0], $teams[3]);
                $spiele[] = array($teams[1], $teams[4]);
                $spiele[] = array($teams[2], $teams[5]);
                $spiele[] = array($teams[3], $teams[6]);
                $spiele[] = array($teams[0], $teams[4]);
                $spiele[] = array($teams[1], $teams[5]);
                $spiele[] = array($teams[2], $teams[6]);
                $spiele[] = array($teams[0], $teams[5]);
                $spiele[] = array($teams[1], $teams[6]);
                break;
        }

        return $spiele;
    }
}


