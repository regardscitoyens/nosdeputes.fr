<?
$alreadyline = 0;
function depile_assoc($asso, $breakline) {
  global $alreadyline;
  foreach (array_keys($asso) as $k) {
    if (!$alreadyline && $k == $breakline) {
      echo "\n";
      $alreadyline = 1;
    }
    echo "<$k>";
    echo depile($asso[$k], $breakline);
    echo "</$k>";
    if ($k == $breakline) {
      echo "\n";
    }
  }
}

function depile($res, $breakline) {
  if (is_array($res)) {
    if (!isset($res[0])) {
      depile_assoc($res, $breakline);
    }else{
      foreach($res as $r) {
	depile($r, $breakline);
      }
    }
  }else{
    $res = str_replace('<', '&lt;', $res);
    $res = str_replace('>', '&gt;', $res);
    $res = str_replace('&', '&amp;', $res);
    echo $res;
  }
}

depile($res, $breakline);
