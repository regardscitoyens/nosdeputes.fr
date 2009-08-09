<?php
// Dataset definition
$DataSet = new xsPData();
$DataSet->AddPoint($n_presences,"Présences");
$DataSet->AddPoint($n_participations,"Participations");
$DataSet->AddPoint($n_mots,"Milliers de mots");
$DataSet->AddPoint($sessions,"Sessions");
$DataSet->AddSerie("Participations");
$DataSet->AddSerie("Présences");
$DataSet->AddSerie("Milliers de mots");
//$DataSet->SetAbsciseLabelSerie("Semaines");
$DataSet->SetYAxisName("Total par session");
$DataSet->SetXAxisName("Sessions parlementaires");
$Test = new xsPChart(700,230);
$Test->xsSetFontProperties("tahoma.ttf",8);
$Test->setGraphArea(70,30,680,200);
$Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
$Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
$Test->drawGraphArea(255,255,255,TRUE);
$GetData = $DataSet->GetData();
$GetDataDescription = $DataSet->GetDataDescription();
$Test->drawScale($GetData,$GetDataDescription,SCALE_NORMAL,150,150,150,TRUE,0,2);
$Test->drawGrid(4,TRUE,230,230,230,50);
$Test->xsSetFontProperties("tahoma.ttf",6);
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
$Test->drawLineGraph($GetData,$GetDataDescription);
$Test->drawPlotGraph($GetData,$GetDataDescription,3,2,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",8);
$Test->drawLegend(75,35,$GetDataDescription,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",10);
$Test->drawTitle(60,22,'Présence de '.$parlementaire->nom.' au cours des dernières sessions',50,50,50,585);
$Test->xsRender('presence-commission-sessions-'.$parlementaire->slug.'.png');

echo image_tag('tmp/xspchart/presence-commission-sessions-'.$parlementaire->slug.'.png', 'alt="Presence en commission de '.$parlementaire->nom.' au cours des dernières sessions"');
# le div fait 820px par 150px a voir si on le modifie ou pas
?>