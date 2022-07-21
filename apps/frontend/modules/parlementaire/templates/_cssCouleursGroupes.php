<style type="text/css">
<?php
$synthese = " ";
foreach (myTools::getGroupesColorMap() as $acro => $col) {
  $acro = strtolower($acro);
  $col = 'rgb('.$col.')';
  echo ".c_$acro { color: $col; } .c_b_$acro { background-color: $col; } .synthese .c_$acro { border-left: 5px solid $col; } .list_orgas td span.c_$acro { width: 10px; height: 10px; display: inline-block; margin-right: 10px; background-color: $col; }";
  if ($synthese != " ") $synthese .= ", ";
  $synthese .= ".synthese .c_$acro";
}
echo $synthese." { padding: 1px 2px; color: #6F6F6F; };"
?>
</style>
