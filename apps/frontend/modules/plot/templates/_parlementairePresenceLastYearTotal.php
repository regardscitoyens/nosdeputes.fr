<?php
$n = count($labels);
$presences = array_fill(1, $n, 0);
$participations = array_fill(1, $n, 0);
$mots = array_fill(1, $n, 0);
for ($i = 1; $i < $n; $i++) {
  $presences[$i] = $n_presences['hemicycle'][$i] + $n_presences['commission'][$i];
  $participations[$i] = $n_participations['hemicycle'][$i] + $n_participations['commission'][$i];
  $mots[$i] = $n_mots['hemicycle'][$i] + $n_mots['commission'][$i];
}
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($presences, "Serie2");
$DataSet->SetSerieName("Présences relevées", "Serie2");
$DataSet->AddPoint($participations, "Serie3");
$DataSet->SetSerieName("Participations", "Serie3");
$DataSet->AddPoint($mots, "Serie4");
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
$Test->setFixedScale(0,13,13);
$Test->xsSetFontProperties("tahoma.ttf",13);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,TRUE,1,TRUE);
$Test->drawGrid(0,TRUE,0,0,0,100);
$Test->setColorPalette(0,50,50,50);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30,100);
if (isset($fonctions)) {
  $total = array_sum($presences);
  if ($total != 0) $fonction = (int)(4*($fonctions['hemicycle']+$fonctions['commission'])/$total);
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
  $Test->drawOverlayBarGraph($Data3,$DataDescr3,85,25);
  $Test->drawLegend(85,108,$DataDescr3,255,255,255);
}
$Test->drawTreshold(0.01,0,0,0,FALSE,FALSE,0);
$Test->xsSetFontProperties("tahoma.ttf",16);
$Test->drawTitle(240,30,"Participation globale au cours de l'année passée (hémicycle et commissions)",50,50,50,585);
$Test->xsRender('participation-globale-'.$parlementaire->slug.'.png');

if (isset($link))
  echo link_to(image_tag('tmp/xspchart/participation-globale-'.$parlementaire->slug.'.png', 'alt="Participation globale de '.$parlementaire->nom.'"'), '@plot_parlementaire_presences?slug='.$parlementaire->slug);
else echo image_tag('tmp/xspchart/participation-globale-'.$parlementaire->slug.'.png', 'alt="Participation globale de '.$parlementaire->nom.'"');
?>
