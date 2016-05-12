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

    function drawFrontPage($turnier){

        $sum = 0;
        foreach($turnier->ownBewerb as $bewerb){
            $sum += count($bewerb->ownTeam);
        }


        $this->SetFont('Arial','B',20);
        $this->Cell(60,10,utf8_decode($turnier->name). " | ".$sum.".Teams",0,0,'L');
        $this->SetFont('');

        $this->Ln();

        // Colors, line width and bold font
        $this->SetFillColor(249,225,158);
        $this->SetTextColor(0);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        $this->SetFont('Arial','',11);

        // Header
        $header = array('Bewerb', "Anzahl Teams");
        $w = array(90, 50);
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

        $sum = 0;
        foreach($turnier->ownBewerb as $bewerb){
            $val = "";
            $x = 0;

            foreach($header as $titel){
                $align = "L";

                if($titel == "Bewerb"){
                    $val = $bewerb->name;
                }else if($titel == "Anzahl Teams"){
                    $val = count($bewerb->ownTeam);
                }
                $this->Cell($w[$x],6,utf8_decode($val),'LR',0,$align,$fill);
                $x++;
            }

            $this->Ln();
            $fill = !$fill;
        }

//        $this->Cell(90,6,utf8_decode($sum),'LR',0,"L",false);

        $this->Cell(array_sum($w),0,'','T');
    }

    function drawBewerbAnmeldungen($Bewerb)
    {
        $this->SetFont('Arial','B',20);
        $this->Cell(60,10,utf8_decode($Bewerb->name),0,0,'L');
        $this->Cell(215,10,utf8_decode(count($Bewerb->ownTeam))." Teams",0,0,'R');

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
        $header = array('ID', 'Teamname', "Spieler", "Herkunft","Zeitpunkt","Ort","Online","Anw.");
        $w = array(10, 40, 85,55,35,15,20,10);
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

        foreach($Bewerb->ownTeam as $anmeldung)
        {
            $anmeldung->setDisplayFields(false);

            $val = "";
            $x = 0;

            foreach($header as $titel){

                $align = "L";

                if($titel == "Handy")$titel = "Handynummer";

                if($titel == "Spieler"){
                    $val = $anmeldung->spieler;
                }else if($titel == "ID"){
                    $val = $anmeldung->id;
                }else if($titel == "Ort"){
                    $align = "R";
                    $val = ($anmeldung->bezahlt_vorort!=0)?$anmeldung->bezahlt_vorort:"";
                }else if($titel == "Zeitpunkt"){
                    $val = date_format(date_create($anmeldung->angemeldet), "d.m.Y H:i");
                   // $val = "";
                }else if($titel == "Teamname") {
                    $val = $anmeldung->display["Teamname"];
                }else if($titel == "Online"){
                    $align = "R";
                    $val = utf8_decode($anmeldung->bezahlt_betrag."/".$anmeldung->nenngeld_gesamt);
                }else if($titel == "Anw."){
                    $val = ($anmeldung->anwesend)?"X":"";
                }else{
                    $val = $anmeldung->display[$titel];
               //         $val =  $anmeldung->getValueFromTitel($titel);
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

$pdf->AddPage("L");
$pdf->drawFrontPage($turnier);


$id = 1;
foreach($turnier->ownBewerb as $Bewerb){
    $pdf->AddPage("L");
    $pdf->drawBewerbAnmeldungen($Bewerb);
    $id++;
}

$pdf->Output();
?>