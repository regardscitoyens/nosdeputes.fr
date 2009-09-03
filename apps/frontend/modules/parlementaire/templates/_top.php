<?php
      $top = $parlementaire->getTop();
if (!$top)
  return ;
if (!$parlementaire->fin_mandat) {
  echo '<h3>Best/Worst (sur 12 derniers mois)</h3>';
  $rank = 1;
 } else {
  $rank = 0;
  $weeks = (strtotime($parlementaire->fin_mandat) - strtotime($parlementaire->debut_mandat))/(60*60*24*7);
  echo '<h3>Bilan de ses '.$weeks.' semaines de mandat </h3>';
 }
?>
<ul><?php
foreach(array_keys($top) as $k) {
  echo '<li style="';
  if ($rank && $top[$k]['rank'] <= 150) 
    echo 'color: green';
  if ($rank && $top[$k]['rank'] >= $top[$k]['max_rank'] - 150) 
    echo 'color: red';
  echo '">';
  echo $k.' : '.$top[$k]['value'];
  if ($rank)
    echo ' ('.$top[$k]['rank'].'ème/'.$top[$k]['max_rank'].')';
  echo '</li>';
}?></ul>

