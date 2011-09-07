<?php

$data = unserialize(get_component('plot', 'getGroupesData', array('type' => $type)));
if ($type === "home") {
  $xsize = 433;
  $ydefsize = 320;
  $yadd = 0;
} else {
  $xsize = 720;
  $yadd = 16;
  $ydefsize = 320;
}
$ysize = $ydefsize + $yadd;
$font = 8;

if ($drawAction === "map") {  
  $Test = new xsPChart($xsize,$ysize);  
  $Test->getImageMap($mapId, TRUE);
}

$DataSet = new xsPData();
$DataSet->AddPoint($data['titres'], "Serie1");
$ct = 2;
foreach ($data['groupes'] as $groupe => $values) {
  $serie = "Serie".$ct;
  $DataSet->AddPoint($values, $serie);
  $DataSet->AddSerie($serie);
  $DataSet->SetSerieName($groupe, $serie);
  $ct++;
}
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisUnit(" %");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($data['totaux'], "Serie".$ct);
$DataSet2->SetAbsciseLabelSerie("Serie".$ct);
$DataSet2->SetYAxisUnit(" %");

$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$Data2 = $DataSet2->getData();
$DataDescr2 = $DataSet2->GetDataDescription();

$Test = new xsPChart($xsize,$ysize);
$Test->setGraphArea(40+2*$font,15+3*$font+$yadd,$xsize-20,$ysize-4*$font);
$Test->drawFilledRectangle(7,7,$xsize-7,$ysize-7,240,240,240);
$Test->drawGraphArea(190,190,190,FALSE);
$Test->setFixedScale(0,100.7,4);
$Test->xsSetFontProperties("tahoma.ttf",$font);
$Test->drawScale($Data2,$DataDescr2,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,FALSE);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,TRUE);
$Test->xsSetFontProperties("tahoma.ttf",$font+1);
$Test->drawTitle(4+2*$font,$ysize-4*$font+18, "TOTAL :",50,50,50);
if ($type === "all") {
  $Test->drawTitle(70,28,"Députés",50,50,50);
  $Test->drawTitle(130,28,"Commission",50,50,50);
  $Test->drawTitle(208,28,"Hémicycle interventions",50,50,50);
  $Test->drawTitle(376,28,"Amendements",50,50,50); 
  $Test->drawTitle(490,28,"Propositions",50,50,50); 
  $Test->drawTitle(600,28,"Questions",50,50,50);
  $Test->xsSetFontProperties("tahoma.ttf",$font+5);
//  $Test->drawTitle(60,30,"Répartition de l'activité des députés sur les 12 derniers mois par groupe politique",25,25,25);
}
$Test->drawGrid(4,TRUE,0,0,0,30);
$ct = 0;
foreach ($data['couleurs'] as $col) if (preg_match('/^(\d+),(\d+),(\d+)$/', $col, $cols)) {
  $Test->setColorPalette($ct,$cols[1],$cols[2],$cols[3]);
  $ct++;
}
$Test->setImageMap(TRUE,$mapId);
$Test->drawStackedBarGraph($Data,$DataDescr,75,90);

//$Test->drawTitle($pos_titre,3 + 2*$font,"Participation globale au cours de".$duree." (hémicycle et commissions)",50,50,50,585);

$Test->xsStroke();

?>
