<?php

$data = unserialize(get_component('plot', 'getGroupesData'));
$xsize = 433; $ysize = 300 ; $font = 8;

if ($drawAction === "map") {  
  $Test = new xsPChart($xsize,$ysize);  
  $Test->getImageMap($mapId);
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
$Test->setGraphArea(40+2*$font,15+4*$font,$xsize-20,$ysize-4*$font);
$Test->drawFilledRectangle(7,7,$xsize-7,$ysize-7,240,240,240);
$Test->drawGraphArea(190,190,190,FALSE);
$Test->setFixedScale(0,100.7,4);
$Test->xsSetFontProperties("tahoma.ttf",$font);
$Test->drawScale($Data2,$DataDescr2,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,FALSE);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,16,0,TRUE,1,TRUE);
$Test->xsSetFontProperties("tahoma.ttf",$font+1);
$Test->drawTitle(4+2*$font,$ysize-4*$font+18, "TOTAL :",50,50,50);
$Test->drawGrid(4,TRUE,0,0,0,40);
$Test->setColorPalette(0,30,30,200);
$Test->setColorPalette(1,30,190,255);
$Test->setColorPalette(2,255,50,190);
$Test->setColorPalette(3,255,30,30);
$Test->setColorPalette(4,130,130,130);
$Test->setImageMap(TRUE,$mapId);
$Test->drawStackedBarGraph($Data,$DataDescr,75,90);

//$Test->drawTitle($pos_titre,3 + 2*$font,"Participation globale au cours de".$duree." (hémicycle et commissions)",50,50,50,585);

$Test->xsStroke();

?>
