<style type="text/css">
<?php
$synthese = " ";
foreach (myTools::getGroupesInfos() as $gpe) {
  $acro = strtolower($gpe[1]);
  $col = $gpe[2];
  echo ".c_$acro { color: rgb($col); } .c_b_$acro { background-color: rgb($col); } .synthese .c_$acro { border-left: 5px solid rgb($col); }";
  if ($synthese != " ") $synthese .= ", ";
  $synthese .= ".synthese .c_$acro";
}
echo $synthese." { padding-left: 2px; padding-right: 2px; color: #6F6F6F; };"
?>
</style>
