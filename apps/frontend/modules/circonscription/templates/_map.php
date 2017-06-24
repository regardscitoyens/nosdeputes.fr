<?php
if (!isset($num) || !$num)
  return;

if (preg_match('/^\d$/', $num))
  $num = sprintf("%02d",$num);
$fixednum = (preg_match('/\d[a-z]/i', $num) ? '0'.$num : sprintf('%03d',$num));

CirconscriptionActions::echoCircoMap($fixednum, $size, 0);
