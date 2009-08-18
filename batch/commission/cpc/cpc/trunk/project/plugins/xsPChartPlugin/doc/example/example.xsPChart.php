<?php
/**
 * @link http://pchart.sourceforge.net/documentation.php
 * @filesource
 */

$DataSet = new xsPData;
$DataSet->AddPoint(array(10,2,3,5,3),"Serie1");
$DataSet->AddPoint(array("Jan","Feb","Mar","Apr","May"),"Serie2");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie("Serie2");

// Initialise the graph
$Test = new xsPChart(380,200);
$Test->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);
$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);

// Draw the pie chart
$Test->xsSetFontProperties("tahoma.ttf", 8);
$GetData = $DataSet->GetData();
$GetDataDescription = $DataSet->GetDataDescription();
$Test->drawPieGraph($GetData,$GetDataDescription,150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);
$Test->drawPieLegend(310,15,$GetData,$GetDataDescription,250,250,250);

$Test->xsRender("example10.png");

?>

<?php echo xspchart_image_tag('example10.png'); ?>





<?php

// Dataset definition
$DataSet = new xsPData;
//$DataSet->ImportFromCSV(sfConfig::get('sf_xspchart_lib_dir') . "/Sample/bulkdata.csv",",",array(1,2,3),FALSE,0);
$DataSet->AddPoint(array(10,2,3,5,3),"Serie1");
$DataSet->AddPoint(array(10,2,3,8,3),"Serie2");
$DataSet->AddPoint(array(10,2,3,4,3),"Serie3");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie();
$DataSet->SetSerieName("January","Serie1");
$DataSet->SetSerieName("February","Serie2");
$DataSet->SetSerieName("March","Serie3");
$DataSet->SetYAxisName("Average age");
$DataSet->SetYAxisUnit("µs");

// Initialise the graph
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

// Draw the 0 line
$Test->xsSetFontProperties("tahoma.ttf",6);
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the line graph

$Test->drawLineGraph($GetData,$GetDataDescription);
$Test->drawPlotGraph($GetData,$GetDataDescription,3,2,255,255,255);

// Finish the graph
$Test->xsSetFontProperties("tahoma.ttf",8);
$Test->drawLegend(75,35,$GetDataDescription,255,255,255);
$Test->xsSetFontProperties("tahoma.ttf",10);
$Test->drawTitle(60,22,"example 1",50,50,50,585);
$Test->xsRender("example1.png");

?>
<?php echo xspchart_image_tag('example1.png'); ?>