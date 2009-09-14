<?php $sf_response->setTitle('La liste des députés "'.$prof.'"'); ?>
<h1>Liste des députés "<i><?php echo $prof; ?></i>"</h1>
<?php
  $nResults = count($parlementaires);
  if ($exact == 1) : ?>
    <p><?php echo $nResults; ?> trouvé<?php if ($nResults > 1) echo 's'; ?> parmi les parlementaires</p>
<?php else : ?>
<p><?php echo $nResults; ?> parlementaire<?php if ($nResults > 1) echo 's'; ?> exerçant une profession comme <em>"<?php echo $prof; ?>"</em></p>
<?php endif; ?>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php if ($exact == 0) echo ucfirst($parlementaire->profession)."&nbsp;: "; echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>, <?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li>
<?php endforeach; ?>
</ul>
<?php
  $nResults2 = count($citoyens);
  if ($nResults2 > 0) { ?>
<p>Sur <?php echo link_to('NosDéputés.fr', '@homepage'); ?> il y a <?php if ($nResults > 0) echo 'également'; ?> <?php echo $nResults2.' <em>"'.$prof; if ($nResults2 > 1) echo 's'; ?>"</em> parmi les citoyens inscrits</p>
<ul>
<?php foreach($citoyens as $citoyen) : ?>
<li><?php echo link_to($citoyen->login, '@citoyen?slug='.$citoyen->slug); ?> (<?php echo $citoyen->activite; ?>)</li>
<?php endforeach; ?>
</ul>
<?php } ?>
