<?php
$DataSet = new xsPData();
$DataSet->AddPoint($labels, "Serie1");
$DataSet->AddPoint($presences, "Serie2");
$DataSet->AddPoint($participations, "Serie3");
$DataSet->AddPoint($mots, "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Séances par semaine");

$DataSetBordure = new xsPData();
$DataSetBordure->AddPoint($labels, "Serie1");
$DataSetBordure->AddPoint($presences, "Serie2");
$DataSetBordure->AddPoint(array_fill(1, count($labels), 0), "Serie5");
$DataSetBordure->AddSerie("Serie2");
$DataSetBordure->AddSerie("Serie5");
$DataSetBordure->SetAbsciseLabelSerie("Serie1");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($labels, "Serie1");
$DataSet2->AddPoint($vacances, "Serie6");
$DataSet2->AddSerie("Serie6");
$DataSet2->SetAbsciseLabelSerie("Serie1");

if (isset($n_questions) && $type != 'commission') {
  $questions = 1;
  $DataSet3 = new xsPData();
  $DataSet3->AddPoint($labels, "Serie1");
  $DataSet3->AddPoint($n_questions, "Serie7");
  $DataSet3->AddSerie("Serie7");
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
$fonction = 0;
if (isset($fonctions)) {
  $total = array_sum($presences);
  if ($total != 0) {
    if ($type == 'total') {
      $totalfonctions = $fonctions['hemicycle']+$fonctions['commission'];
    } else {
      $totalfonctions = $fonctions[$type];
    }
    $fonction = (int)(4*$totalfonctions/$total);
  }
} 
$Test->setColorPalette(0,255,35*$fonction,0);
$Test->setColorPalette(1,255,255,0);
$Test->setColorPalette(2,0,255,0);
$Test->drawFilledLineGraph($Data,$DataDescr,78);
if ($questions == 1) {
  $Test->setColorPalette(0,0,0,255);
  $Test->drawOverlayBarGraph($Data3,$DataDescr3,85,25);
}
$Test->setColorPalette(0,255,35*$fonction,0);
$Test->setColorPalette(1,0,0,0);
$Test->drawLineGraph($DataBordure,$DataDescrBordure);
$Test->xsSetFontProperties("tahoma.ttf",$font + 3);
if ($time == 'lastyear') {
  $duree = "l'année passée";
  $shortduree = 'annee';
} else {
  $duree = "la session ".preg_replace('/^(\d{4})/', '\\1-', $time);
  $shortduree = $time;
}
if ($type == 'total') {
  $Test->drawTitle(240,3 + 2*$font,"Participation globale au cours de ".$duree." (hémicycle et commissions)",50,50,50,585);
  $titre = 'globale-'.$shortduree;
} else {
  $titre = $type;
  if ($type == 'commission') $titre .= 's';
  $Test->drawTitle(270,3 + 2*$font,"Participation en ".$titre." au cours de ".$duree,50,50,50,585);
  $titre .= '-'.$shortduree;
}
$filename = 'participation-'.$titre.'-'.$parlementaire->slug.isset($link).'.png';
$Test->xsRender($filename);

if (isset($link))
  echo link_to(image_tag('tmp/xspchart/'.$filename, 'alt="Participation '.$titre.' de '.$parlementaire->nom.'"'), '@plot_parlementaire_presences?slug='.$parlementaire->slug.'&time=lastyear');
else echo image_tag('tmp/xspchart/'.$filename, 'alt="Participation '.$titre.' de '.$parlementaire->nom.'"');
echo "<p><span style='background-color: rgb(255,";
echo 35*$fonction.",0);'>&nbsp;</span> ";
if ($type == 'commission') echo '&nbsp;Présences enregistrées&nbsp;&nbsp;&nbsp;'; else echo '&nbsp;Présences relevées&nbsp;&nbsp;&nbsp;';
echo "<span style='background-color: rgb(255,200,0);'>&nbsp;</span>&nbsp;Participations&nbsp;&nbsp;&nbsp;";
echo "<span style='background-color: rgb(0,255,0);'>&nbsp;</span>&nbsp;Mots prononcés (x&nbsp;10&nbsp;000)&nbsp;&nbsp;&nbsp;";
if ($questions == 1)
    echo "<span style='background-color: rgb(0,0,255);'>&nbsp;</span>&nbsp;Questions orales&nbsp;&nbsp;&nbsp;";
echo "<span style='background-color: rgb(150,150,150);'>&nbsp;</span>&nbsp;Vacances parlementaires";
if (isset($link))
  echo "&nbsp;&nbsp;&nbsp;&nbsp;".link_to('Explications', '@plot_parlementaire_presences?slug='.$parlementaire->slug.'&time=lastyear');
echo "</p>"
?>
