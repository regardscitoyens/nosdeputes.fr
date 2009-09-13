<div class="temp">
<div class="liste_deputes">
<?php if (isset($circos)) : ?>
<p>Toutes les circonscriptions électorales</p>
<table>
<tr><td width="23%"><ul>
<?php $div = floor(count($circos)/4)+1; $ct = 0; foreach($circos as $num => $circo) : $ct++?>
<li><?php echo link_to($circo, '@list_parlementaires_circo?search='.$num).' ('.$num.')'; ?></li>
<?php if ($ct == $div || $ct == (2*$div) || $ct == (3*$div)) echo '</ul></td><td width="23%"><ul>'; ?>
<?php endforeach; ?>
</ul></td></tr>
</table>
<?php else : ?>
<?php if ($num != 0) : ?>
  <img class="carte_departement" src="http://www.assemblee-nationale.fr/13/qui/circonscriptions/<?php printf('%03d-%03d', $num, $num); ?>-1.gif" alt="$circo"/>
<?php endif; ?>
<?php
  $nResults = count($parlementaires);
  if ($nResults == 0) : ?>
<p>Aucune circonscription trouvée pour <em>"<?php echo $circo; ?>"</em></p>
  <?php else : ?>
<p><?php echo $circo; ?><?php if ($num != 0) echo ' ('.$num.')'; ?>&nbsp;: <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; if ($num != 0) echo ' pour '.$n_circo.' circonscriptions'; ?></p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php if ($num == 0) {echo $parlementaire->getNomNumCirco()." - "; } echo $parlementaire->getNumCircoString(1); ?>&nbsp;: <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php endif; ?>
</div>
</div>