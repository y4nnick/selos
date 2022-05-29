<?php

require_once("core/core.php");

header('Content-Type: text/html; charset=UTF-8');
define('FPDF_FONTPATH','font/');
error_reporting(E_ALL);
require('fpdf/fpdf.php');

class ownPDF extends FPDF
{
    function Footer()
    {
        if($this->PageNo() != 1){
            $this->SetY(-15);

            $this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'R');
        }
        //$this->Image('logonew.png',1,190,60);
    }

    function drawTeilnehmer($teilnehmer)
    {
        $this->SetFont('Arial','B',20);
       // $this->Cell(60,10,utf8_decode($Bewerb->name),0,0,'L');
      //  $this->Cell(215,10,utf8_decode(count($Bewerb->ownTeam))." Teams",0,0,'R');

        $this->SetFont('Arial','B',16);
      //  $this->Cell(40,10,$Bewerb->name);
        $this->Ln();

        // Colors, line width and bold font
        $this->SetFillColor(249,225,158);
        $this->SetTextColor(0);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        $this->SetFont('Arial','',11);

        // Header
        $header = array('Nachname', "Vorname", "Kaution erhalten");
        $w = array(50, 50, 50);
        for($i=0;$i<count($header);$i++){
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        }

        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Data
        $fill = false;
        foreach($teilnehmer as $t)
        {

            $val = "";
            $x = 0;

            foreach($header as $titel){
                $val = "";
                $align = "L";

                switch($titel){
                    case "Nachname": $val = $t->nachname; break;
                    case "Vorname": $val = $t->vorname; break;
                    default: break;
                }

                $this->Cell($w[$x],6,utf8_decode($val),'LR',0,$align,$fill);
                $x++;
            }

            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }
}

$turnier = R::load("turnier",1);

$pdf = new ownPDF();
$pdf->AliasNbPages();

$pdf->AddPage("P");

$teilnehmer = R::find("spieler","ORDER BY nachname");

$tArray = [];

foreach($teilnehmer as $t){

   // echo $t->team->gemeinschaft_id."<br>";

    if($t->team->gemeinschaft_id == 1){
        continue;
    }

    $t->vorname = trim($t->vorname);
    $t->nachname = trim($t->nachname);

   /* echo "---------------------<br>";
    echo "t_Vorname: ".$t->vorname."<br>";
    echo "t_Nachname: ".$t->nachname."<br>";*/

    $vorhanden = false;

    foreach($tArray as $inner){

        //echo "i_Vorname: ".$inner->vorname."<br>";
        //echo "i_Nachname: ".$inner->nachname."<br>";
        if(strcmp($inner->vorname,$t->vorname) == 0 && strcmp($inner->nachname,$t->nachname) == 0){
      //      echo "VORHANDEN<br>";
            $vorhanden = true;
            break;
        }
    }

    if(!$vorhanden){
        $tArray[] = $t;
    }


}


$pdf->drawTeilnehmer($tArray);

/*
$id = 1;
foreach($turnier->ownBewerb as $Bewerb){
    $pdf->AddPage("L");
    $pdf->drawBewerbAnmeldungen($Bewerb);
    $id++;
}*/

$pdf->Output();
?>