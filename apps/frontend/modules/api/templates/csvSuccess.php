<?php
if (isset($GET['withBOM']) && $GET['withBOM']) {
  printf("\xef\xbb\xbf");
}
if (!isset($multi)) {
  $multi = array();
 }
if (!isset($champs)) {
  $champs = $res[$champ];
 }
foreach(array_keys($champs) as $key) 
{
  echo "$key;";
}
echo "\n";

function depile_assoc($asso, $breakline, $multi) {
  global $alreadyline;
  $semi = 0;
  foreach (array_keys($asso) as $k) {
    if (isset($multi[$k]) && $multi[$k]) {
      $semi = 1;
    }
    depile($asso[$k], $breakline, $multi, $semi);
    if ($k == $breakline) {
      echo "\n";
    }
  }
  return $semi;
}

function depile($res, $breakline, $multi, $comma = 0) {
  if (is_array($res)) {
    if (isset($res['organisme']) && isset($res['fonction']))
      return depile($res['organisme']." - ".$res['fonction'], $breakline, $multi, $comma);
    if (!isset($res[0])) {
      if (array_keys($res)) 
	return depile_assoc($res, $breakline, $multi);
      echo ";";
      return;
    }
    foreach($res as $r)
      $semi = depile($r, $breakline, $multi);
    if ($semi) 
      echo ';';
  }else{
    if ($comma)
      $res = preg_replace('/[,;]/', '', $res);
    $string = preg_match('/[,;"]/', $res);
    if ($string) {
      $res = preg_replace('/"/', '\"', $res);
      echo '"';
    }
    echo $res;
    if ($string)
      echo '"';
    if ($comma) 
      echo '|';
    else echo ';';
  }
}

depile($res, $breakline, $multi);
