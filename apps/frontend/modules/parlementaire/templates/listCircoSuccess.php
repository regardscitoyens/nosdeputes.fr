<div class="temp">
<?php if ($num != 0) : ?>
  <img class="carte_departement" src="http://www.assemblee-nationale.fr/13/qui/circonscriptions/<?php printf('%03d-%03d', $num, $num); ?>-1.gif" alt="$circo"/>
<?php endif; ?>
<?php
  $nResults = count($parlementaires);
  if ($nResults == 0) : ?>
<p>Aucune circonscription trouvée pour <em>"<?php echo $circo; ?>"</em></p>
  <?php else : ?>
<p><?php echo $circo; ?><?php if ($num != 0) echo " (".$num.")&nbsp;: ".$n_circo." circonscriptions et "; else echo "&nbsp;: "; echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php if ($num == 0) {echo $parlementaire->getNomNumCirco()." -"; } echo $parlementaire->getNumCircoString(1); ?>&nbsp;: <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
</div>