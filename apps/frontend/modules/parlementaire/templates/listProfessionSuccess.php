<p><? $nResults = $pager->getNbResults(); echo $nResults; ?> parlementaire<? if ($nResults > 1) echo 's'; ?> exerçant la profession de <em>"<? echo $prof; ?>"</em></p>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo $parlementaire->getStatut(); ?> <? echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?>, <? echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li><? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <a href="<?php echo url_for('parlementaire/list') ?>?profession=<? echo $prof; ?>&page=1">
   <<
    </a>

    <a href="<?php echo url_for('parlementaire/list') ?>?profession=<? echo prof; ?>&page=<?php echo $pager->getPreviousPage() ?>">
   <
    </a>

    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="<?php echo url_for('parlementaire/list') ?>?profession=<? echo $prof; ?>&page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>

    <a href="<?php echo url_for('parlementaire/list') ?>?profession=<? echo $prof; ?>&page=<?php echo $pager->getNextPage() ?>">
   >
    </a>

    <a href="<?php echo url_for('parlementaire/list') ?>?profession=<? echo $prof; ?>&page=<?php echo $pager->getLastPage() ?>">
   >>
    </a>
</div>
<? endif; ?>