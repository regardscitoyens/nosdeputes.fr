<?php
if ((isset($GET['withBOM']) && $GET['withBOM']) || (isset($withBOM) && $withBOM))
  printf("\xef\xbb\xbf");

if (!isset($multi))
  $multi = array();

if (!isset($champs))
  $champs = $res[$breakline];

myTools::depile_csv($res, $breakline, $multi, $champs);
?>
