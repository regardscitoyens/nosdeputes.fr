<div class="temp">
<?php $nResults = $pager->getNbResults(); ?>
<?php if ($orga->type == 'groupe') : ?>
<p>Groupe politique <?php echo $orga->getNom(); ?> (<?php echo $orga->getSmallNomGroupe(); ?>) : <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></p>
<?php else : if ($orga->type == 'extra') : ?>
<p>Organisme extra-parlementaire <?php echo $orga->getNom(); ?> : <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></p>
<?php else : ?>
<p>Groupe parlementaire <?php echo $orga->getNom(); ?> : <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></p>
<?php endif; endif; ?>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <?php echo link_to('<< ', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page=1'); ?>
    <?php echo link_to('< ', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
</div>