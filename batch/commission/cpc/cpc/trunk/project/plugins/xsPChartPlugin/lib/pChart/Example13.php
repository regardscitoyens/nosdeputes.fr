<?php
 /*
     Example13: A 2D exploded pie graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(10,2,3,5,3),"Serie1");
 $DataSet->AddPoint(array("Jan","Feb","Mar","Apr","May"),"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie("Serie2");

 // Initialise the graph
 $Test = new pChart(300,200);
 $Test->drawFilledRoundedRectangle(7,7,293,193,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,295,195,5,230,230,230);

 // Draw the pie chart
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawFlatPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),120,100,60,PIE_PERCENTAGE,10);
 $Test->drawPieLegend(230,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

 $Test->Render("example13.png");
?>