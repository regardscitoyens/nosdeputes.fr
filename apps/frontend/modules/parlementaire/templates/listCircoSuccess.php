<div class="liste_deputes">
<?php if (isset($circos)) : ?>
<h1>Toutes les circonscriptions électorales</h1><?php $sf_response->setTitle('Toutes les circonscriptions électorales'); ?>
<table>
<tr><td style="width:23%;"><ul>
<?php $div = floor(count($circos)/4)+1; $ct = 0; foreach($circos as $num => $circo) : $ct++ ; $num = sprintf("%02d",$num) ?>
<li><?php echo link_to($circo, '@list_parlementaires_circo?search='.$num).' ('.$num.')'; ?></li>
<?php if ($ct == $div || $ct == (2*$div) || $ct == (3*$div)) echo '</ul></td><td style="width:23%;"><ul>'; ?>
<?php endforeach; ?>
</ul></td></tr>
</table>
<?php else : ?>
<?php $nResults = count($parlementaires);
 if ($num != 0 && $nResults != 0) : ?>
  <img class="carte_departement" src="http://www.assemblee-nationale.fr/13/qui/circonscriptions/<?php if (preg_match('/^(\d+)(\w+)$/', $num, $match)) printf ('%02d%s-%02d%s', $match[1], $match[2], $match[1], $match[2]); else printf('%03d-%03d', $num, $num); ?>-1.gif" alt="$circo"/>
<?php endif; ?>
<h1>Les députés par circonscriptions</h1>
<?php
$sf_response->setTitle('Les députés par circonscriptions');
  if ($nResults == 0) : ?>
<p>Aucune circonscription trouvée pour <em>"<?php if ($circo != '') echo $circo; else echo $num; ?>"</em></p>
  <?php else : ?>
<p><?php echo $circo; ?><?php if ($num != 0) echo ' ('.sprintf("%02d",$num).')'; ?>&nbsp;: <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; if ($num != 0) echo ' pour '.$n_circo.' circonscriptions'; ?></p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php if ($num == 0) {echo $parlementaire->getNomNumCirco()." - "; } echo $parlementaire->getNumCircoString(1); ?>&nbsp;: <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php endif; ?>
</div>
