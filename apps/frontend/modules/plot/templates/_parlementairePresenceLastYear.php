<?php

// Dataset definition
$president = false;
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($n_presences_hemicycle, "Serie2");
if ($president == true) {
  $DataSet->SetSerieName("Présidences", "Serie2");
} else $DataSet->SetSerieName("Invectives", "Serie2");
$DataSet->AddPoint($n_participations_hemicycle, "Serie3");
$DataSet->SetSerieName("Participations", "Serie3");
$DataSet->AddPoint($n_mots_hemicycle, "Serie4");
$DataSet->SetSerieName("10 000 mots", "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Total par semaine");
$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$DataSet2 = new xsPData();
$DataSet2->AddPoint($labels, "Serie1");
$DataSet2->AddPoint($vacances, "Serie5");
$DataSet2->SetSerieName("mots par 10 000", "Serie5");
$DataSet2->AddSerie("Serie5");
$DataSet2->SetAbsciseLabelSerie("Serie1");
$Data2 = $DataSet2->GetData();
$DataDescr2 = $DataSet2->GetDataDescription();

$Test = new xsPChart(700,230);
$Test = new xsPChart(800,300);
$Test->setGraphArea(70,40,765,280);
$Test->drawFilledRoundedRectangle(7,7,793,293,5,240,240,240);
$Test->drawRoundedRectangle(5,5,795,395,5,230,230,230);
$Test->drawGraphArea(15,15,15,FALSE);
$Test->setFixedScale(0,14,15);
$Test->xsSetFontProperties("tahoma.ttf",13);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,TRUE);
$Test->drawGrid(0,TRUE,0,0,0,3);
$Test->drawTreshold(0.8,255,0,0,FALSE,FALSE,8);
$Test->drawTreshold(4,255,255,0,FALSE,FALSE,8);
$Test->setColorPalette(0,250,250,250);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30);
if ($president == true) {
  $Test->setColorPalette(0,0,255,0);
} else $Test->setColorPalette(0,255,255,0);
$Test->setColorPalette(1,255,0,0);
$Test->setColorPalette(2,0,0,255);
$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawLegend(105,65,$DataDescr,255,255,255);
$Test->drawFilledLineGraph($Data,$DataDescr,80);
$Test->setColorPalette(0,100,100,100);
$Test->setColorPalette(1,100,100,100);
$Test->setColorPalette(2,100,100,100);
$Test->drawLineGraph($Data,$DataDescr);
$Test->drawPlotGraph($Data,$DataDescr,1,1,128,128,128);
$Test->xsSetFontProperties("tahoma.ttf",16);
$Test->drawTitle(270,30,"Assiduité en hémicycle au cours de l'année passée",50,50,50,585);
$Test->xsRender('presence-hemicycle-annee-'.$parlementaire->slug.'.png');

echo image_tag('tmp/xspchart/presence-hemicycle-annee-'.$parlementaire->slug.'.png', 'alt="Assiduité en hémicycle de '.$parlementaire->nom.'"');
/*
// Dataset definition
$DataSet = new xsPData();
$DataSet->AddPoint($n_presences_hemicycle,"Présences");
$DataSet->AddPoint($n_participations_hemicycle,"Participations");
$DataSet->AddPoint($n_mots_hemicycle,"Milliers de mots");
$DataSet->AddPoint($n_vacances,"Vacances");
$DataSet->AddPoint($semaines,"Semaines");
$DataSet->AddSerie("Milliers de mots");
$DataSet->AddSerie("Participations");
$DataSet->AddSerie("Présences");
$DataSet->AddSerie("Vacances");
//$DataSet->SetAbsciseLabelSerie("Semaines");
$DataSet->SetYAxisName("Total par semaine");
$DataSet->SetXAxisName("Semaines");
$Test = new xsPChart(700,230);
$Test->xsSetFontProperties("tahoma.ttf",8);
$Test->setGraphArea(70,30,680,200);
$Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
$Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
$Test->drawGraphArea(255,255,255,TRUE);
$GetData = $DataSet->GetData();
$GetDataDescription = $DataSet->GetDataDescription();
$Test->drawScale($GetData,$GetDataDescription,SCALE_NORMAL,150,150,150,TRUE,0,2);
$Test->drawGrid(4,TRUE,230,230,230,50);
$Test->xsSetFontProperties("tahoma.ttf",6);
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
$Test->drawLineGraph($GetData,$GetDataDescription);
$Test->drawPlotGraph($GetData,$GetDataDescription,3,2,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",8);
$Test->drawLegend(75,35,$GetDataDescription,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",10);
$Test->drawTitle(60,22,"Présence en hémicycle de '.$parlementaire->nom.' au cours de l'année passée",50,50,50,585);
$Test->xsRender('presence-hemicycle-annee-'.$parlementaire->slug.'.png');

echo image_tag('tmp/xspchart/presence-hemicycle-annee-'.$parlementaire->slug.'.png', 'alt="Presence en hémicycle de '.$parlementaire->nom.'"');
*/
?>
