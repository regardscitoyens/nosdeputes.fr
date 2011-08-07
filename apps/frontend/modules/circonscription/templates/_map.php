<?php 
if (!isset($num) || !$num) 
return; 

if (preg_match('/^\d$/', $num))  
$num = sprintf("%02d",$num); 

if (preg_match('/\d[a-z]/i', $num))  
$fixednum = '0'.$num;  
else  
$fixednum = sprintf('%03d',$num); 

CirconscriptionActions::echoCircoMap($fixednum, $size, 0);