<h1>Toutes les circonscriptions électorales</h1><?php 
$sf_response->setTitle('Toutes les circonscriptions électorales'); ?>
<h2>Carte des circonscriptions</h2>
<p><?php include 'circonscriptions/france.html' ?></p>
<h2>Liste des départements</h2>
<div id="circo_table">
<table>
<tr><td><ul>
<?php $div = floor(count($circos)/4)+1; $ct = 0; 
foreach($circos as $num => $circo) 
{
  $ct++ ; 
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<li>'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).' ('.strtoupper($num).')</li>';
  if ($ct == $div || $ct == (2*$div) || $ct == (3*$div)) 
    echo '</ul></td><td style="width:23%;"><ul>'; 
  
} ?>
</ul></td></tr>
</table>
</div>
