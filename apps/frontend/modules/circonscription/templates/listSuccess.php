<div id="carte_circo">
<h1>Toutes les circonscriptions par département</h1><?php 
$sf_response->setTitle('Toutes les circonscriptions électorales par département - NosDéputés.fr'); ?>
<?php include_partial('circonscription/mapDepartement', array('width'=>600, 'height'=>0)); ?>
<div class="list_deptmts">
<?php $iters = array("0" => 27, "27" => 55, "55" => 65, "65" => 75, "75" => 86, "86" => 96, "96" => 120);
$div = floor(count($circos)/6)+1;
foreach ($iters as $iter1 => $iter2) {
 $ct = 0;
 if ($iter1 != 0)
   echo '</div><div class="list_deptmts">';
 if ($iter2 == 120)
   echo '<h3 class="align_center">DOM-TOMs&nbsp;:</h3>';
 foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= $iter1)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p title="'.strtoupper($num).'" class="dept dep_map jstitle" id="dep'.strtoupper($num).'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == $iter2)
    break; 
 }
} ?>
</div>
</div>
