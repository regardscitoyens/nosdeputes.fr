<div class="temp">
<p><?php $nResults = $pager->getNbResults(); echo $nResults; ?> parlementaire<?php if ($nResults > 1) echo 's'; ?> exerçant la profession de <em>"<?php echo $prof; ?>"</em></p>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(); ?> <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>, <?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li><?php endforeach ; ?>
</ul>
<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <?php echo link_to('<< ', '@list_parlementaires_profession?profession='.$prof.'&page=1'); ?>
    <?php echo link_to('< ', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_profession?profession='.$prof.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
</div>