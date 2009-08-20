<?php
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($presences, "Serie2");
if ($type == 'commission')
  $DataSet->SetSerieName("Présences enregistrées", "Serie2");
else $DataSet->SetSerieName("Présences relevées", "Serie2");
$DataSet->AddPoint($participations, "Serie3");
$DataSet->SetSerieName("Participations", "Serie3");
$DataSet->AddPoint($mots, "Serie4");
$DataSet->SetSerieName("Nombre de mots (x10000)", "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Séances par semaine");

$DataSetBordure = new xsPData();
$DataSetBordure->AddPoint($labels, "Serie1");
$DataSetBordure->AddPoint($presences, "Serie2");
$DataSetBordure->AddSerie("Serie2");
$DataSetBordure->SetAbsciseLabelSerie("Serie1");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($labels, "Serie1");
$DataSet2->AddPoint($vacances, "Serie5");
$DataSet2->AddSerie("Serie5");
$DataSet2->SetAbsciseLabelSerie("Serie1");

if (isset($n_questions) && $type != 'commission') {
  $questions = 1;
  $DataSet3 = new xsPData();
  $DataSet3->AddPoint($labels, "Serie1");
  $DataSet3->AddPoint($n_questions, "Serie6");
  $DataSet3->SetSerieName("Questions orales", "Serie6");
  $DataSet3->AddSerie("Serie6");
  $DataSet3->SetAbsciseLabelSerie("Serie1");
  $Data3 = $DataSet3->GetData();
  $DataDescr3 = $DataSet3->GetDataDescription();
} else $questions = 0;

$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$DataBordure = $DataSetBordure->GetData();
$DataDescrBordure = $DataSetBordure->GetDataDescription();
$Data2 = $DataSet2->GetData();
$DataDescr2 = $DataSet2->GetDataDescription();

if (isset($link)) {
  $font = 9;
  $ticks = 2;
  $scale = 14;
  $size = 150;
} else {
  $font = 12;
  $ticks = 1;
  if ($type =='total') {
    $scale = 14;
    $size = 300;
  } else if ($type == 'commission') {
    $scale = 7;
    $size = 185;  // 70+$scale/$scaletotal*($sizetotal-70)
  } else if ($type == 'hemicycle') {
    $scale = 12;
    $size = 267;
  }
}

$Test = new xsPChart(800,$size);
$Test->setGraphArea(25+3*$font,3*$font,780,$size-10-2*$font);
$Test->drawFilledRoundedRectangle(7,7,793,$size-7,5,240,240,240);
$Test->drawRoundedRectangle(5,5,795,$size - 5,5,230,230,230);
$Test->drawGraphArea(230,230,230,FALSE);
$Test->setFixedScale(0,$scale,$scale/$ticks);
$Test->xsSetFontProperties("tahoma.ttf",$font);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,FALSE,1,TRUE);
$Test->drawGrid(0,TRUE,0,0,0,100);
$Test->setColorPalette(0,50,50,50);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30,100);
if (isset($fonctions)) {
  $total = array_sum($presences);
  if ($total != 0) {
    if ($type == 'total') {
      $totalfonctions = $fonctions['hemicycle']+$fonctions['commission'];
    } else {
      $totalfonctions = $fonctions[$type];
    }
    $fonction = (int)(4*$totalfonctions/$total);
  } else $fonction = 0;
  $Test->setColorPalette(0,255,35*$fonction,0);
} else $Test->setColorPalette(0,255,0,0);
$Test->setColorPalette(1,255,255,0);
$Test->setColorPalette(2,0,255,0);
$Test->drawFilledLineGraph($Data,$DataDescr,75);
$Test->drawLineGraph($DataBordure,$DataDescrBordure);
$Test->xsSetFontProperties("tahoma.ttf",$font);
$Test->drawLegend(10+5*$font,2+3*$font,$DataDescr,255,255,255);
if ($questions == 1) {
  $Test->setColorPalette(0,0,0,255);
  $Test->drawOverlayBarGraph($Data3,$DataDescr3,85,25);
  $Test->drawLegend(10+5*$font,8*$font-2,$DataDescr3,255,255,255);
}
$Test->drawTreshold(0.01,0,0,0,FALSE,FALSE,0);
$Test->xsSetFontProperties("tahoma.ttf",$font + 3);
if ($type == 'total') {
  $Test->drawTitle(240,3 + 2*$font,"Participation globale au cours de l'année passée (hémicycle et commissions)",50,50,50,585);
  $titre = 'globale';
} else {
  $titre = $type;
  if ($type == 'commission') $titre .= 's';
  $Test->drawTitle(270,3 + 2*$font,"Participation en '.$titre.' au cours de l'année passée",50,50,50,585);
}
$Test->xsRender('participation-'.$titre.'-'.$parlementaire->slug.'.png');

if (isset($link))
  echo link_to(image_tag('tmp/xspchart/participation-'.$titre.'-'.$parlementaire->slug.'.png', 'alt="Participation '.$titre.' de '.$parlementaire->nom.'"'), '@plot_parlementaire_presences?slug='.$parlementaire->slug);
else echo image_tag('tmp/xspchart/participation-'.$titre.'-'.$parlementaire->slug.'.png', 'alt="Participation '.$titre.' de '.$parlementaire->nom.'"');
?>
