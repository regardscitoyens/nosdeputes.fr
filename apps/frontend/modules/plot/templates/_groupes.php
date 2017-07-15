<?php
if ($empty)
  return ;
$isOrga = preg_match('/orga/', $plot);
$isComm = preg_match('/seance_com/', $plot);
$hasInter = (array_sum($interventions) != 0);
if ($isComm) {
  $DataSet = new xsPData();
  $DataSet->AddPoint($labels, "Serie1");
  $DataSet->AddPoint($presences, "Serie2");
  $DataSet->AddSerie("Serie2");
  $DataSet->SetAbsciseLabelSerie("Serie1");
  $Data = $DataSet->GetData();
  $DataDescr = $DataSet->GetDataDescription();
}
if ($hasInter) {
  $DataSet2 = new xsPData();
  $DataSet2->AddPoint($labels, "Serie1");
  $DataSet2->AddPoint($interventions, "Serie2");
  $DataSet2->AddSerie("Serie2");
  $DataSet2->SetAbsciseLabelSerie("Serie1");
  $Data2 = $DataSet2->GetData();
  $DataDescr2 = $DataSet2->GetDataDescription();
}
if (isset($membres)) {
  $DataSet = new xsPData();
  $DataSet->AddPoint($labels, "Serie1");
  $DataSet->AddPoint($parls, "Serie2");
  $DataSet->AddSerie("Serie2");
  $DataSet->SetAbsciseLabelSerie("Serie1");
  $Data = $DataSet->GetData();
  $DataDescr = $DataSet->GetDataDescription();
} else if ($hasInter) {
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
$xsize = 405;
$xtitre = 25;
$ysize = 190;
$ylegend = 45;
$x0 = 170;
$y0 = 110;
$filename .= '-'.$plot.'.png';
$titre = 'par groupes du travail de cette '.$seancenom;
if (preg_match('/section/', $plot)) {
  $xtitre = 38;
  $titre = 'par groupes du travail sur ce dossier';
} else if ($isComm) {
  if (!$hasInter) {
    $xsize = 250;
    $xtitre = 48;
    $titre = 'des présents';
  } else {
    $xsize = 550;
    $xtitre = 60;
    $titre .= ' de commission';
  }
} else if ($isOrga) {
  $xsize = 250;
  $xtitre = 48;
  $titre = 'par groupes';
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
  $Test->drawFlatPieGraph($Data,$DataDescr,$x0,$y0,47,PIE_VALUES,0,0,157);
  $Test->drawFilledCircle($x0+1,$y0+1,15,240,240,240);
  $x0 += 150;
}
if (isset($Data2)) {
  $Test->drawFlatPieGraph($Data2,$DataDescr2,$x0,$y0,47,PIE_VALUES,0,0,157);
  $Test->drawFilledCircle($x0+1,$y0+1,15,240,240,240);
}
$x0 += 150;
if (isset($Data3)) {
  $Test->drawFlatPieGraph($Data3,$DataDescr3,$x0,$y0,47,PIE_PERCENTAGE,0,0,157);
  $Test->drawFilledCircle($x0+1,$y0+1,15,240,240,240);
}
$Test->xsSetFontProperties("tahoma.ttf",9);
$ct = 0;
foreach ($couleurs as $col) if (preg_match('/^(\d+),(\d+),(\d+)$/', $col, $cols)) {
  $Test->setColorPalette($ct,$cols[1],$cols[2],$cols[3]);
  $ct++;
}
$Test->drawLegend(15,$ylegend,$DataDescrLegend,255,255,255);

$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawTitle($xtitre,25,'Répartition '.$titre,50,50,50);
$Test->xsSetFontProperties("tahoma.ttf",8);
if ($isOrga)
  $Test->drawTitle(148,155,'Membres',50,50,50);
if (preg_match('/(section|seance_hemi)/', $plot)) {
  $Test->drawTitle(139,160,'Interventions',50,50,50);
  $Test->drawTitle(280,152,'Temps de parole',50,50,50);
  $Test->drawTitle(280,166,'(mots prononcés)',50,50,50);
} else if ($isComm) {
  $Test->drawTitle(149,160,'Présents',50,50,50);
  $Test->drawTitle(289,160,'Interventions',50,50,50);
  $Test->drawTitle(431,152,'Temps de parole',50,50,50);
  $Test->drawTitle(431,166,'(mots prononcés)',50,50,50);
}
$Test->xsRender($filename);
if ($isComm && !isset($nolink))
  echo link_to(image_tag('tmp/xspchart/'.$filename, array('alt'=>"Répartition ".$titre, 'style'=>'height: '.$ysize.'px;')), '@presents_seance?seance='.$seancecom);
else echo image_tag('tmp/xspchart/'.$filename, array('alt'=>'Répartition '.$titre, 'style'=>'height: '.$ysize.'px;'));

if (!isset($nolegend))
 echo include_partial('plot/groupesLegende', array("groupes" => $labels, "width" => $xsize));
?>
