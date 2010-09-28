<?php

$data = unserialize(get_component('plot', 'groupesData'));

$data['groupes'];
$data['totaux'];
$data['titres'];

$n = count($data['titres']);

$xsize = 390;
$xtitre = 80; $ysize = 300; $ylegend = 145; $x0 = 140; $y0 = 112;
$ticks = 12.5;
$scale = 14;

if ($drawAction === "map" ) {  
  $Test = new xsPChart(800,$size);  
  $Test->getImageMap($mapId);
}

$DataSet = new xsPData();
$DataSet->AddPoint($data['titres'], "Serie1");
$DataSet->AddPoint($presences, "Serie2");
$DataSet->AddPoint($participations, "Serie3");
$DataSet->AddPoint($mots, "Serie4");
$DataSet->AddSerie("Serie2");
$DataSet->AddSerie("Serie3");
$DataSet->AddSerie("Serie4");
$DataSet->SetAbsciseLabelSerie("Serie1");
$DataSet->SetYAxisName("Séances par semaine");

$DataSetLegend = new xsPData();
$DataSetLegend->AddPoint($data['labels'], "Serie1");
$DataSetLegend->AddPoint($presences, "Serie2");
$DataSetLegend->AddPoint($participations, "Serie3");
$DataSetLegend->AddSerie("Serie2");
$DataSetLegend->AddSerie("Serie3");
$DataSetLegend->SetSerieName(utf8_decode('Présences'),"Serie2");
$DataSetLegend->SetSerieName("Participations","Serie3");
$DataSetLegend->SetAbsciseLabelSerie("Serie1");

$DataSetBordure = new xsPData();
$DataSetBordure->AddPoint($data['labels'], "Serie1");
$DataSetBordure->AddPoint($presences, "Serie2");
$DataSetBordure->AddPoint(array_fill(1, count($data['labels']), 0), "Serie5");
$DataSetBordure->AddSerie("Serie2");
$DataSetBordure->AddSerie("Serie5");
$DataSetBordure->SetAbsciseLabelSerie("Serie1");

$DataSet2 = new xsPData();
$DataSet2->AddPoint($data['labels'], "Serie1");
$DataSet2->AddPoint($data['vacances'], "Serie6");
$DataSet2->AddSerie("Serie6");
$DataSet2->SetAbsciseLabelSerie("Serie1");

if ($questions === 'true' && $type != 'commission') {
  $DataSet3 = new xsPData();
  $DataSet3->AddPoint($data['labels'], "Serie1");
  $DataSet3->AddPoint($data['n_questions'], "Serie7");
  $DataSet3->AddSerie("Serie7");
  $DataSet3->SetAbsciseLabelSerie("Serie1");
  $Data3 = $DataSet3->GetData();
  $DataDescr3 = $DataSet3->GetDataDescription();
}

$Data = $DataSet->GetData();
$DataDescr = $DataSet->GetDataDescription();
$DataBordure = $DataSetBordure->GetData();
$DataDescrBordure = $DataSetBordure->GetDataDescription();
$Data2 = $DataSet2->GetData();
$DataDescr2 = $DataSet2->GetDataDescription();
$DataLegend = $DataSetLegend->GetData();
$DataDescrLegend = $DataSetLegend->GetDataDescription();

$Test = new xsPChart(800,$size);
$Test->setGraphArea(25+3*$font,3*$font,780,$size-10-2*$font);
$Test->drawFilledRoundedRectangle(7,7,793,$size-7,5,240,240,240);
$Test->drawRoundedRectangle(5,5,795,$size - 5,5,230,230,230);
$Test->drawGraphArea(230,230,230,FALSE);
$Test->setFixedScale(0,$scale,$scale/$ticks);
$Test->xsSetFontProperties("tahoma.ttf",$font);
$Test->drawScale($Data,$DataDescr,SCALE_NORMAL,50,50,50,TRUE,0,0,FALSE,1,TRUE);
if ($link === 'true') {
  $Test->setColorPalette(0,255,255,255);
  $Test->setColorPalette(1,255,255,255);
  $Test->setImageMap(TRUE,$mapId);
  $Test->drawOverlayBarGraph($DataLegend,$DataDescrLegend,30,100);
  $Test->setImageMap(FALSE,$mapId);
}  
$Test->drawGrid(0,TRUE,0,0,0,100);
$Test->setColorPalette(0,50,50,50);
$Test->drawOverlayBarGraph($Data2,$DataDescr2,30,100);
$Test->setColorPalette(0,255,0,0);
$Test->setColorPalette(1,255,255,0);
$Test->setColorPalette(2,0,255,0);
$Test->drawFilledLineGraph($Data,$DataDescr,78);
if ($questions === 'true' && $type != 'commission') {
  $Test->setColorPalette(0,0,0,255);
  $Test->drawOverlayBarGraph($Data3,$DataDescr3,85,25);
}
$Test->setColorPalette(0,255,0,0);
$Test->setColorPalette(1,0,0,0);
$Test->drawLineGraph($DataBordure,$DataDescrBordure);

$Test->xsSetFontProperties("tahoma.ttf",$font + 3);
$pos_titre = 240;
if ($time === 'lastyear') {
  if (isset($data['mandat_clos'])) {
    $pos_titre = 210;
    $duree = ' sa dernière année de mandat';
  } else $duree = 's 12 derniers mois';
  $shortduree = 'annee';
} else {
  $duree = " la session ".preg_replace('/^(\d{4})/', '\\1-', $time);
  $shortduree = $time;
}
if ($type === 'total') {
  $Test->drawTitle($pos_titre,3 + 2*$font,"Participation globale au cours de".$duree." (hémicycle et commissions)",50,50,50,585);
  $titre = 'globale-'.$shortduree;
} else {
  $titre = $type;
  if ($type === 'commission') $titre .= 's';
  $Test->drawTitle($pos_titre+30,3 + 2*$font,"Participation en ".$titre." au cours de".$duree,50,50,50,585);
  $titre .= '-'.$shortduree;
}
if ($link === 'true')
  $Test->setImageMap(TRUE,$mapId);

$Test->xsStroke();

?>
