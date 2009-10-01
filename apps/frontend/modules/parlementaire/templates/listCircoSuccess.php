<div class="liste_deputes">
<?php if (isset($circos)) : ?>
<h1>Toutes les circonscriptions électorales</h1><?php $sf_response->setTitle('Toutes les circonscriptions électorales'); ?>
<table>
<tr><td style="width:23%;"><ul>
<?php $div = floor(count($circos)/4)+1; $ct = 0; foreach($circos as $num => $circo) : $ct++ ; if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num); ?>
<li><?php echo link_to($circo, '@list_parlementaires_circo?search='.$num).' ('.strtoupper($num).')'; ?></li>
<?php if ($ct == $div || $ct == (2*$div) || $ct == (3*$div)) echo '</ul></td><td style="width:23%;"><ul>'; ?>
<?php endforeach; ?>
</ul></td></tr>
</table>
<p><?php include 'circonscriptions/france.html' ?></p>
<?php else : ?>
<?php $nResults = count($parlementaires);
 if ($num != 0 && $nResults != 0) : ?>
 <?php if (!preg_match('/{\d}3/', $num)) $fixednum = '0'.$num; else $fixednum = $num;
  include 'circonscriptions/'.$fixednum.'.html'; ?>
<?php endif; ?>
<h1>Les députés par circonscriptions</h1>
<?php
$sf_response->setTitle('Les députés par circonscriptions');
  if ($nResults == 0) : ?>
<p>Aucune circonscription trouvée pour <em>"<?php if ($circo != '') echo $circo; else echo $num; ?>"</em></p>
  <?php else : ?>
<p>
<?php echo $circo; ?>
<?php if ($num != 0) echo ' ('.sprintf("%02d",$num).')'; 
if ($num_circo > 0) echo  ', '.$parlementaires[0]->getNumCircoString(1).':';
else echo '&nbsp;:';
?>

<?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's';
if ($num != 0 && $num_circo == 0) echo ' pour '.$n_circo.' circonscriptions';
?>
</p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php
if ($num == 0)
  echo link_to($parlementaire->getNomNumCirco(), '@list_parlementaires_circo?search='.$parlementaire->getNumDepartement())." - ";
echo $parlementaire->getNumCircoString(1); ?>
&nbsp;:
<?php
echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); 
?>
&nbsp;(<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php endif; ?>
</div>
