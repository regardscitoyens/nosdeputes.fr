<?php
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($n_presences['commission'], "Serie2");
$DataSet->SetSerieName("Présences enregistrées", "Serie2");
$DataSet->AddPoint($n_participations['commission'], "Serie3");
$DataSet->SetSerieName("Participations", "Serie3");
$DataSet->AddPoint($n_mots['commission'], "Serie4");
$DataSet->SetSerieName("Nombre de mots (x10000)", "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Séances par semaine");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($labels, "Serie1");
$DataSet2->AddPoint($vacances, "Serie5");
$DataSet2->AddSerie("Serie5");
$DataSet2->SetAbsciseLabelSerie("Serie1");

$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$Data2 = $DataSet2->GetData();
$DataDescr2 = $DataSet2->GetDataDescription();

$Test = new xsPChart(800,300);
$Test->setGraphArea(70,40,780,260);
$Test->drawFilledRoundedRectangle(7,7,793,293,5,240,240,240);
$Test->drawRoundedRectangle(5,5,795,395,5,230,230,230);
$Test->drawGraphArea(230,230,230,FALSE);
$Test->setFixedScale(0,7,7);
$Test->xsSetFontProperties("tahoma.ttf",13);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,TRUE);
$Test->drawGrid(0,TRUE,0,0,0,100);
$Test->setColorPalette(0,50,50,50);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30,100);
if (isset($fonctions)) {
  $total = array_sum($n_presences['commission']);
  if ($total != 0) $fonction = (int)(4*$fonctions['commission']/$total);
  else $fonction = 0;
  $Test->setColorPalette(0,255,35*$fonction,0);
} else $Test->setColorPalette(0,255,0,0);
$Test->setColorPalette(1,255,255,0);
$Test->setColorPalette(2,0,255,0);
$Test->drawFilledLineGraph($Data,$DataDescr,90);
$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawLegend(85,55,$DataDescr,255,255,255);
$Test->drawTreshold(0.01,0,0,0,FALSE,FALSE,0);
$Test->xsSetFontProperties("tahoma.ttf",16);
$Test->drawTitle(270,30,"Participation en commissions au cours de l'année passée",50,50,50,585);
$Test->xsRender('participation-commission-'.$parlementaire->slug.'.png');

echo image_tag('tmp/xspchart/participation-commission-'.$parlementaire->slug.'.png', 'alt="Participation en commissions de '.$parlementaire->nom.'"');
?>
