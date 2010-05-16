<?

function depile_assoc($asso) {
  $s = '{';
  foreach (array_keys($asso) as $k) {
    $s .= '"'.$k.'":';
    $s .= depile($asso[$k]);
    $s .= ",";
  }
  return substr($s, 0, -1).'}';
}

function depile($res) {
  if (is_array($res)) {
    if (!isset($res[0])) {
      return depile_assoc($res);
    }else{
      $s = '[';
      foreach($res as $r) {
	$s .= depile($r).',';
      }
      return substr($s, 0, -1).']';
    }
  }else{
    if (is_numeric($res)) {
      return $res;
    }
    return '"'.$res.'"';
  }
}

echo depile($res);