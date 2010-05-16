<?

function depile_assoc($asso) {
  foreach (array_keys($asso) as $k) {
    echo "<$k>";
    echo depile($asso[$k]);
    echo "</$k>";
  }
}

function depile($res) {
  if (is_array($res)) {
    if (!isset($res[0])) {
      depile_assoc($res);
    }else{
      foreach($res as $r) {
	depile($r);
      }
    }
  }else{
    echo $res;
  }
}

depile($res);