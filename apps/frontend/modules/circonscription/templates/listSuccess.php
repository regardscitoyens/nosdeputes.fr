<h1>Toutes les circonscriptions électorales</h1><?php 
$sf_response->setTitle('Toutes les circonscriptions électorales'); ?>
<table>
<tr><td style="width:23%;"><ul>
<?php $div = floor(count($circos)/4)+1; $ct = 0; 
foreach($circos as $num => $circo) 
{
  $ct++ ; 
  echo '<li>'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).' ('.strtoupper($num).')</li>';
  if ($ct == $div || $ct == (2*$div) || $ct == (3*$div)) 
    echo '</ul></td><td style="width:23%;"><ul>'; 
  
} ?>
</ul></td></tr>
</table>
<p><?php echo link_to('Version carte', '@list_parlementaires_circo_france'); ?></p>
