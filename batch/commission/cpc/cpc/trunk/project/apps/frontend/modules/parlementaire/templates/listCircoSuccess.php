<p><?php echo $circo; ?> : <?php $nResults = $pager->getNbResults(); echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?></p>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo $parlementaire->getNumCircoString(); ?> : <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo $parlementaire->getStatut(); ?> <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <?php echo link_to('<<', '@list_parlementaires_circo?nom_circo='.$circo.'&page=1'); ?>
    <?php echo link_to('<', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('>', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getNextPage()); ?>
    <?php echo link_to('>>', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>