<p><?php $nResults = $pager->getNbResults(); echo $nResults; ?> résultat<?php if ($nResults > 1) echo 's'; ?> <?php if ($search) echo 'pour <em>"'.$search.'"</em>'; ?></p>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(); ?> <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>, <?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <?php echo link_to('<< ', '@search_parlementaire?search='.$search.'&page=1'); ?>
    <?php echo link_to('< ', '@search_parlementaire?search='.$search.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@search_parlementaire?search='.$search.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', '@search_parlementaire?search='.$search.'&page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', '@search_parlementaire?search='.$search.'&page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>