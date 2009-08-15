<div class="temp">
<?php
  $nResults = count($parlementaires);
  if ($exact == 1) : ?>
<p><?php echo $nResults; ?> <em>"<?php if ($nResults > 1) $prof = preg_replace('/s?$/','s', $prof); echo $prof; ?>"</em> parmi les parlementaires (et les citoyens, à venir)</p>
<?php else : ?>
<p><?php echo $nResults; ?> parlementaire<?php if ($nResults > 1) echo 's'; ?> (et citoyens a venir) exerçant une profession comme <em>"<?php echo $prof; ?>"</em></p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php if ($exact == 0) echo ucfirst($parlementaire->profession)."&nbsp;: "; echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>, <?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li><?php endforeach ; ?>
</ul>
</div>