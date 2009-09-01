<div class="temp">
<?php $nResults = $pager->getNbResults(); ?>
<h1><?php echo $orga->getNom(); ?></h1>
<?php include_component('article', 'show', array('categorie'=>'Organisme', 'object_id'=>$orga->id)); ?>
<?php if ($orga->type == 'extra') : ?>
<h2>Organisme extra-parlementaire composé de <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></h2>
<?php else : ?>
<h2>Mission parlementaire composée de <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></h2>
<?php endif; ?>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(1).", ".link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li>
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