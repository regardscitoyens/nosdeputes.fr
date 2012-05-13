<?php
if ($empty)
  return ;
if ($plot == 'total' || (preg_match('/seance_com/', $plot))) { $DataSet = new xsPData();
  $DataSet->AddPoint($labels, "Serie1"); $DataSet->AddPoint($presences, "Serie2");
  $DataSet->AddSerie("Serie2"); $DataSet->SetAbsciseLabelSerie("Serie1");
  $Data = $DataSet->GetData(); $DataDescr = $DataSet->GetDataDescription();
}
if (array_sum($interventions) != 0) { $DataSet2 = new xsPData();
  $DataSet2->AddPoint($labels, "Serie1"); $DataSet2->AddPoint($interventions, "Serie2");
  $DataSet2->AddSerie("Serie2"); $DataSet2->SetAbsciseLabelSerie("Serie1");
  $Data2 = $DataSet2->GetData(); $DataDescr2 = $DataSet2->GetDataDescription();
}
if ($plot == 'total') { $DataSetBis = new xsPData();
  $DataSetBis->AddPoint($labels, "Serie1"); $DataSetBis->AddPoint($presences_moy, "Serie2");
  $DataSetBis->AddSerie("Serie2"); $DataSetBis->SetAbsciseLabelSerie("Serie1");
  $DataBis = $DataSetBis->GetData(); $DataDescrBis = $DataSetBis->GetDataDescription();
  $DataSet2Bis = new xsPData();
  $DataSet2Bis->AddPoint($labels, "Serie1"); $DataSet2Bis->AddPoint($interventions_moy, "Serie2");
  $DataSet2Bis->AddSerie("Serie2"); $DataSet2Bis->SetAbsciseLabelSerie("Serie1");
  $Data2Bis = $DataSet2Bis->GetData(); $DataDescr2Bis = $DataSet2Bis->GetDataDescription();
} else if (array_sum($interventions) != 0) { $DataSet3 = new xsPData();
  $DataSet3->AddPoint($labels, "Serie1"); $DataSet3->AddPoint($temps, "Serie2");
  $DataSet3->AddSerie("Serie2"); $DataSet3->SetAbsciseLabelSerie("Serie1");
  $Data3 = $DataSet3->GetData(); $DataDescr3 = $DataSet3->GetDataDescription();
}
$DataSetLegend = new xsPData();
foreach($labels as $groupe) if ($groupe) {
  $DataSetLegend->AddPoint(array(), $groupe);
  $DataSetLegend->AddSerie($groupe);
}
$DataDescrLegend = $DataSetLegend->GetDataDescription();

$filename = 'repartition-groupes';
$xsize = 390;
if ($plot == 'total') {
  $xtitre = 80; $ysize = 360; $ylegend = 145; $x0 = 140; $y0 = 112;
  $duree = "l'année passée";
  $shortduree = 'annee';
  $filename .= '-'.$shortduree.'.png';
  $titre = 'du travail parlementaire';
} else {
  $xtitre = 25; $ysize = 190; $ylegend = 60; $x0 = 155; $y0 = 110;
  $filename .= '-'.$plot.'.png';
  $titre = 'par groupe du travail de cette séance';
  if (preg_match('/section/', $plot)) {
    $xtitre = 28; $xtitre = 38;
    $titre = 'par groupe du travail sur ce dossier';
  } else if (preg_match('/com/', $plot)) {
    if (array_sum($interventions) == 0) {
      $xsize = 250;  $xtitre = 48;
      $titre = 'des présents';
    } else {
      $xsize = 550; $xtitre = 60;
      $titre .= ' de commission';
    }
  }
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
  $Test->drawFlatPieGraph($Data,$DataDescr,$x0,$y0,50,PIE_VALUES,0,0,150);
  $Test->drawFilledCircle($x0,$y0+2,12,240,240,240);
  if ($plot == 'total') {
    $Test->drawFlatPieGraph($DataBis,$DataDescrBis,$x0,260,50,PIE_VALUES,0,0,150);
    $Test->drawFilledCircle($x0,262,12,240,240,240);
  }
  $x0 += 150;
}
if (isset($Data2)) {
  $Test->drawFlatPieGraph($Data2,$DataDescr2,$x0,$y0,50,PIE_VALUES,0,0,150);
  $Test->drawFilledCircle($x0,$y0+2,12,240,240,240);
}
if ($plot == 'total') {
  $Test->drawFlatPieGraph($Data2Bis,$DataDescr2Bis,$x0,260,50,PIE_VALUES,0,0,150);
  $Test->drawFilledCircle($x0,262,12,240,240,240);
}
$x0 += 150;
if (isset($Data3)) {
  $Test->drawFlatPieGraph($Data3,$DataDescr3,$x0,$y0,50,PIE_PERCENTAGE,0,0,150,150);
  $Test->drawFilledCircle($x0,$y0+2,12,240,240,240);
}
$Test->xsSetFontProperties("tahoma.ttf",9);
$ct = 0;
foreach ($couleurs as $col) if (preg_match('/^(\d+),(\d+),(\d+)$/', $col, $cols)) {
  $Test->setColorPalette($ct,$cols[1],$cols[2],$cols[3]);
  $ct++;
}
$Test->drawFilledRoundedRectangle(15,$ylegend-14,72,$ylegend+5,5,255,255,255);
$Test->drawLegend(15,$ylegend,$DataDescrLegend,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",10);
$Test->drawTitle(20,$ylegend,'Groupes',0,0,0);

$Test->xsSetFontProperties("tahoma.ttf",12);
if ($plot != 'total')
$Test->drawTitle($xtitre,25,'Répartition '.$titre,50,50,50);
#if ($plot == 'total')
#  $Test->drawTitle(107,46,'au cours de '.$duree,50,50,50);
$Test->xsSetFontProperties("tahoma.ttf",9);
if ($plot == 'total') {
  $Test->drawTitle(105,190,'Semaines de',50,50,50);
  $Test->drawTitle(113,202,'présence',50,50,50);
  $Test->drawTitle(251,190,'Interventions',50,50,50);
  $Test->drawTitle(258,202,'en séance',50,50,50);
} else {
  $Test->xsSetFontProperties("tahoma.ttf",8);
  if (preg_match('/(section|seance_hemi)/', $plot)) {
    $Test->drawTitle(120,166,'Interventions',50,50,50);
    $Test->drawTitle(268,158,'Temps de parole',50,50,50);
    $Test->drawTitle(268,172,'(mots prononcés)',50,50,50);
  } else if (preg_match('/seance_com/', $plot)) {
    $Test->drawTitle(135,166,'Présents',50,50,50);
    $Test->drawTitle(275,166,'Interventions',50,50,50);
    $Test->drawTitle(415,158,'Temps de parole',50,50,50);
    $Test->drawTitle(415,172,'(mots prononcés)',50,50,50);
  }
}
if ($plot == 'total') {
  $Test->xsSetFontProperties("tahoma.ttf",11);
  $Test->drawTitle(110,340,"Activité moyenne d'un député",50,50,50);
}
$Test->xsRender($filename);
if ($plot == 'total')
  echo link_to(image_tag('tmp/xspchart/'.$filename, array('alt'=>"Répartition ".$titre, 'style'=>'width: '.$xsize.'px; height: '.$ysize.'px;')), '@top_global');
 else if (preg_match('/com/', $plot) && !isset($nolink))
   echo link_to(image_tag('tmp/xspchart/'.$filename, array('alt'=>"Répartition ".$titre, 'style'=>'width: '.$xsize.'px; height: '.$ysize.'px;')), '@presents_seance?seance='.$seance);
 else echo image_tag('tmp/xspchart/'.$filename, array('alt'=>'Répartition '.$titre, 'style'=>'width: '.$xsize.'px; height: '.$ysize.'px;'));

echo include_partial('plot/groupesLegende', array());
?>
