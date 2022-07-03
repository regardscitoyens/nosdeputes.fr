<?php
if ($empty)
  return ;
$isGrpe = ($plot === "groupes");
$isOrga = preg_match('/orga/', $plot);
$isComm = preg_match('/seance_com/', $plot);
$hasWords = (array_sum($temps) >= 200);
if ($isComm) {
  $DataSet = new xsPData();
  $DataSet->AddPoint($labels, "Serie1");
  $DataSet->AddPoint($presences, "Serie2");
  $DataSet->AddSerie("Serie2");
  $DataSet->SetAbsciseLabelSerie("Serie1");
  $Data = $DataSet->GetData();
  $DataDescr = $DataSet->GetDataDescription();
}
if (isset($membres) || $isGrpe) {
  $DataSet = new xsPData();
  $DataSet->AddPoint($labels, "Serie1");
  $DataSet->AddPoint($parls, "Serie2");
  $DataSet->AddSerie("Serie2");
  $DataSet->SetAbsciseLabelSerie("Serie1");
  $Data = $DataSet->GetData();
  $DataDescr = $DataSet->GetDataDescription();
} else if ($hasWords) {
  $DataSet3 = new xsPData();
  $DataSet3->AddPoint($labels, "Serie1");
  $DataSet3->AddPoint($temps, "Serie2");
  $DataSet3->AddSerie("Serie2");
  $DataSet3->SetAbsciseLabelSerie("Serie1");
  $Data3 = $DataSet3->GetData();
  $DataDescr3 = $DataSet3->GetDataDescription();
}
$DataSetLegend = new xsPData();
foreach($labels as $groupe) if ($groupe) {
  $DataSetLegend->AddPoint(array(), $groupe);
  $DataSetLegend->AddSerie($groupe);
}
$DataDescrLegend = $DataSetLegend->GetDataDescription();

$filename = 'repartition-groupes';
$xsize = 300;
$ysize = 210;
$xtitre = 107;
$ytitre = 172;
$ylegend = 20;
$x0 = 190;
$y0 = 100;
$radius = 70;
$filename .= '-'.$plot.'.png';
$titre = 'par groupes';
if ($isComm) {
  if (!$hasWords) {
    $titre = 'des présents';
  } else {
    $xtitre = 200;
    $ytitre = 190;
    $xsize = 500;
  }
} else if ($isOrga) {
  $titre = 'des membres';
  $xtitre = 103;
} else if ($isGrpe) {
  $xsize = 380;
  $ysize = 230;
  $xtitre = 130;
  $ytitre = 195;
  $ylegend = 30;
  $x0 = 230;
  $y0 = 122;
  $radius = 87;
  $titre = "des $total députés";
}

$Test = new xsPChart($xsize,$ysize);
$Test->drawFilledRoundedRectangle(7,7,$xsize-7,$ysize-7,5,240,240,240);
$Test->drawRoundedRectangle(5,5,$xsize-5,$ysize-5,5,230,230,230);
$ct = 0;
foreach ($couleurs as $col) if (preg_match('/^(\d+),(\d+),(\d+)$/', $col, $cols)) {
  $Test->setColorPalette($ct,$cols[1],$cols[2],$cols[3]);
  $ct++;
}
$Test->setColorPalette($ct,240,240,240);
$Test->xsSetFontProperties("tahoma.ttf",7);
if (isset($Data)) {
  $Test->drawFlatPieGraph($Data,$DataDescr,$x0,$y0,$radius,PIE_VALUES,0,0,158);
  $Test->drawFilledCircle($x0+1,$y0+1,$radius/3,240,240,240);
  $x0 += 200;
}
if (isset($Data3)) {
  $Test->drawFlatPieGraph($Data3,$DataDescr3,$x0,$y0,$radius,PIE_PERCENTAGE,0,0,158,166.7);
  $Test->drawFilledCircle($x0+1,$y0+1,$radius/3,240,240,240);
}
$Test->xsSetFontProperties("tahoma.ttf",9);
$ct = 0;
foreach ($couleurs as $col) if (preg_match('/^(\d+),(\d+),(\d+)$/', $col, $cols)) {
  $Test->setColorPalette($ct,$cols[1],$cols[2],$cols[3]);
  $ct++;
}
$Test->drawLegend(20,$ylegend,$DataDescrLegend,255,255,255);

$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawTitle($xtitre,$ytitre,'Répartition '.$titre,50,50,50);
$Test->xsSetFontProperties("tahoma.ttf",8);
if (preg_match('/(section|seance_hemi)/', $plot)) {
  $Test->drawTitle(160,147,'Temps de parole',50,50,50);
  $Test->drawTitle(158,161,'(mots prononcés)',50,50,50);
} else if ($isComm && $hasWords) {
  $Test->drawTitle(170,155,'Présents',50,50,50);
  $Test->drawTitle(352,147,'Temps de parole',50,50,50);
  $Test->drawTitle(350,161,'(mots prononcés)',50,50,50);
}
$Test->xsRender($filename);
if ($isComm && !isset($nolink))
  echo link_to(image_tag('tmp/xspchart/'.$filename, array('alt'=>"Répartition ".$titre, 'style'=>'height: '.$ysize.'px;')), '@presents_seance?seance='.$seancecom);
else echo image_tag('tmp/xspchart/'.$filename, array('alt'=>'Répartition '.$titre, 'style'=>'height: '.$ysize.'px;'));

if (!isset($nolegend))
 echo include_partial('plot/groupesLegende', array("groupes" => $labels, "width" => $xsize));
?>
