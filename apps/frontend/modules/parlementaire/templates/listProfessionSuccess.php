<p><? $nResults = $pager->getNbResults(); echo $nResults; ?> parlementaire<? if ($nResults > 1) echo 's'; ?> exerçant la profession de <em>"<? echo $prof; ?>"</em></p>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo $parlementaire->getStatut(); ?> <? echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>, <? echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li><? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <? echo link_to('<<', '@list_parlementaires_profession?profession='.$prof.'&page=1'); ?>
    <? echo link_to('<', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_profession?profession='.$prof.'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <? echo link_to('>', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getNextPage()); ?>
    <? echo link_to('>>', '@list_parlementaires_profession?profession='.$prof.'&page='.$pager->getLastPage()); ?>
</div>
<? endif; ?>