<?php
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($n_presences['hemicycle'], "Serie2");
$DataSet->SetSerieName("Présences relevées", "Serie2");
$DataSet->AddPoint($n_participations['hemicycle'], "Serie3");
$DataSet->SetSerieName("Participations", "Serie3");
$DataSet->AddPoint($n_mots['hemicycle'], "Serie4");
$DataSet->SetSerieName("Nombre de mots (x10000)", "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Séances par semaine");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($labels, "Serie1");
$DataSet2->AddPoint($vacances, "Serie5");
$DataSet2->AddSerie("Serie5");
$DataSet2->SetAbsciseLabelSerie("Serie1");

if (isset($n_questions)) {
  $DataSet3 = new xsPData();
  $DataSet3->AddPoint($labels, "Serie1");
  $DataSet3->AddPoint($n_questions, "Serie6");
  $DataSet3->SetSerieName("Questions orales", "Serie6");
  $DataSet3->AddSerie("Serie6");
  $DataSet3->SetAbsciseLabelSerie("Serie1");
  $Data3 = $DataSet3->GetData();
  $DataDescr3 = $DataSet3->GetDataDescription();
}

$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$Data2 = $DataSet2->GetData();
$DataDescr2 = $DataSet2->GetDataDescription();

$Test = new xsPChart(800,300);
$Test->setGraphArea(70,40,780,260);
$Test->drawFilledRoundedRectangle(7,7,793,293,5,240,240,240);
$Test->drawRoundedRectangle(5,5,795,395,5,230,230,230);
$Test->drawGraphArea(230,230,230,FALSE);
$Test->setFixedScale(0,12,12);
$Test->xsSetFontProperties("tahoma.ttf",13);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,TRUE);
$Test->drawGrid(0,TRUE,0,0,0,100);
$Test->setColorPalette(0,50,50,50);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30,100);
if (isset($fonctions)) {
  $total = array_sum($n_presences['hemicycle']);
  if ($total != 0) $fonction = (int)(4*$fonctions['hemicycle']/($total));
  else $fonction = 0;
  $Test->setColorPalette(0,255,35*$fonction,0);
} else $Test->setColorPalette(0,255,0,0);
$Test->setColorPalette(1,255,255,0);
$Test->setColorPalette(2,0,255,0);
$Test->drawFilledLineGraph($Data,$DataDescr,90);
$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawLegend(85,55,$DataDescr,255,255,255);
if (isset($n_questions)) {
  $Test->setColorPalette(0,0,0,255);
  $Test->drawOverlayBarGraph($Data3,$DataDescr3,70,25);
  $Test->drawLegend(85,108,$DataDescr3,255,255,255);
}
$Test->drawTreshold(0.01,0,0,0,FALSE,FALSE,0);
$Test->xsSetFontProperties("tahoma.ttf",16);
$Test->drawTitle(270,30,"Participation en hémicycle au cours de l'année passée",50,50,50,585);
$Test->xsRender('participation-hemicycle-'.$parlementaire->slug.'.png');

echo image_tag('tmp/xspchart/participation-hemicycle-'.$parlementaire->slug.'.png', 'alt="Participation en hémicycle de '.$parlementaire->nom.'"');
?>
