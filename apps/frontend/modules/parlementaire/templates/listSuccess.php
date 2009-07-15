<p><? $nResults = $pager->getNbResults(); echo $nResults; ?> résultat<? if ($nResults > 1) echo 's'; ?> <? if ($search) echo 'pour <em>"'.$search.'"</em>'; ?></p>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo $parlementaire->getStatut(); ?> <? echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>, <? echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li>
<? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <? echo link_to('<<', '@search_parlementaire?search='.$search.'&page=1'); ?>
    <? echo link_to('<', '@search_parlementaire?search='.$search.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@search_parlementaire?search='.$search.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <? echo link_to('>', '@search_parlementaire?search='.$search.'&page='.$pager->getNextPage()); ?>
    <? echo link_to('>>', '@search_parlementaire?search='.$search.'&page='.$pager->getLastPage()); ?>
</div>
<? endif; ?>