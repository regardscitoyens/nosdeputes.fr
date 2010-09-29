<h1>Toutes les circonscriptions par département</h1><?php 
$sf_response->setTitle('Toutes les circonscriptions électorales par département - NosDéputés.fr'); ?>
<?php // CirconscriptionActions::echoCircoMap("full", 900, 0); ?>
<?php CirconscriptionActions::echoDeptmtsMap(600, 546); ?>
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
  echo '<p title="'.strtoupper($num).'" class="dept" id="dep'.strtoupper($num).'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == $iter2)
    break; 
 }
} ?>
</div>
<script type="text/javascript">
/* survol du txt */
$(".dept").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseover();
})
$(".dept").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseout();
})
/* survol de la map */
$("area").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#D1EA74");
})
$("area").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#fff");
})
</script>
