<?php
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
foreach($labels as $groupe) {
  $DataSetLegend->AddPoint(array(), $groupe);
  $DataSetLegend->AddSerie($groupe);
}
$DataDescrLegend = $DataSetLegend->GetDataDescription();

$filename = 'repartition-groupes';
$xsize = 390;
if ($plot == 'total') {
  $xtitre = 25; $ysize = 360; $ylegend = 145; $x0 = 140; $y0 = 115;
  $duree = "l'année passée";
  $shortduree = 'annee';
  $filename .= '-'.$shortduree.'.png';
  $titre = 'du travail parlementaire par groupe';
} else {
  $xtitre = 35; $ysize = 190; $ylegend = 50; $x0 = 155; $y0 = 85;
  $filename .= '-'.$plot.'.png';
  $titre = 'par groupe du travail en séance';
  if (preg_match('/section/', $plot)) {
    $xtitre = 28; $xtitre = 38;
    $titre = 'par groupe du travail sur le dossier';
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
$Test->setColorPalette(0,200,200,200);
$Test->setColorPalette(1,30,30,200);
$Test->setColorPalette(2,30,190,255);
$Test->setColorPalette(3,255,50,190);
$Test->setColorPalette(4,255,30,30);
$Test->xsSetFontProperties("tahoma.ttf",7);
if (isset($Data)) {
  $Test->drawPieGraph($Data,$DataDescr,$x0,$y0,55,PIE_VALUES,TRUE,65,15);
if ($plot == 'total') $Test->drawPieGraph($DataBis,$DataDescrBis,$x0,260,55,PIE_VALUES,TRUE,65,15);
  $x0 += 150;
}
if (isset($Data2))
  $Test->drawPieGraph($Data2,$DataDescr2,$x0,$y0,55,PIE_VALUES,TRUE,65,15);
if ($plot == 'total') $Test->drawPieGraph($Data2Bis,$DataDescr2Bis,$x0,260,55,PIE_VALUES,TRUE,65,15);
$x0 += 150;
if (isset($Data3))
  $Test->drawPieGraph($Data3,$DataDescr3,$x0,$y0,55,PIE_PERCENTAGE,TRUE,65,15);
$Test->xsSetFontProperties("tahoma.ttf",9);
$Test->drawLegend(15,$ylegend,$DataDescrLegend,255,255,255);

$Test->xsSetFontProperties("tahoma.ttf",12);
$Test->drawTitle($xtitre,25,'Répartition '.$titre,50,50,50);
if ($plot == 'total')
  $Test->drawTitle(90,40,'au cours de '.$duree,50,50,50);
$Test->xsSetFontProperties("tahoma.ttf",9);
if ($plot == 'total') {
  $Test->drawTitle(97,190,'Semaines de',50,50,50);
  $Test->drawTitle(107,202,'présence',50,50,50);
  $Test->drawTitle(248,190,'Interventions',50,50,50);
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
  $Test->drawTitle(20,340,"Travail moyen d'un député par groupe parlementaire",50,50,50);
}
$Test->xsRender($filename);
if ($plot == 'total')
  echo link_to(image_tag('tmp/xspchart/'.$filename, 'alt="Répartition '.$titre.'"'), '@top_global');
else echo image_tag('tmp/xspchart/'.$filename, 'alt="Répartition '.$titre.'"');
?>
