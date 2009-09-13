<?php if ($search) { ?>
<h1>Recherche de députés</h1>
<?php $sf_response->setTitle('Recherche de députes "'.$search.'"');
 } else { ?>
<h1>La liste de tous les députés</h1>
<?php $sf_response->setTitle('La liste de tous les députés'); ?>
<?php } ?>
<div class="liste_deputes">
<?php if ($search != "") : ?>
<p><?php $nResults = $pager->getNbResults(); echo $nResults; ?> résultat<?php if ($nResults > 1) echo 's'; ?> pour <em>"<?php echo $search; ?>"</em></p>
<?php else : ?>
<p>Les <?php $nResults = $pager->getNbResults(); echo $nResults; ?> députés de la législature (577 en activité)&nbsp;:</p>
<?php endif; ?>
<?php if (isset($similars) && $similars) {
   echo '<p>Peut être, cherchiez vous : </p><ul>';
   foreach($similars as $s) {
     echo '<li>'.link_to($s['nom'], 'parlementaire/show?slug='.$s['slug']).'</li>'; 
   }
   echo '</ul>';
 }?>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1); ?>, <?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php include_partial('parlementaire/paginate', array('pager' => $pager, 'link' => '@list_parlementaires?search='.$search.'&')); ?>
</div>
