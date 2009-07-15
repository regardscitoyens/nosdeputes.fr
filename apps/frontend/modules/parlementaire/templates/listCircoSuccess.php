<p><? echo $circo; ?> : <? $nResults = $pager->getNbResults(); echo $nResults; ?> député<? if ($nResults > 1) echo 's'; ?></p>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo $parlementaire->getNumCircoString(); ?> : <? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo $parlementaire->getStatut(); ?> <? echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>)</li>
<? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <? echo link_to('<<', '@list_parlementaires_circo?nom_circo='.$circo.'&page=1'); ?>
    <? echo link_to('<', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <? echo link_to('>', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getNextPage()); ?>
    <? echo link_to('>>', '@list_parlementaires_circo?nom_circo='.$circo.'&page='.$pager->getLastPage()); ?>
</div>
<? endif; ?>