<? $nResults = $pager->getNbResults(); ?>
<? if ($orga->type == 'groupe') : ?>
<p>Groupe politique <? echo $orga->getNom(); ?> (<? echo $orga->getSmallNomGroupe(); ?>) : <? echo $nResults; ?> député<? if ($nResults > 1) echo 's'; ?></p>
<? else : if ($orga->type == 'extra') : ?>
<p>Organisme extra-parlementaire <? echo $orga->getNom(); ?> : <? echo $nResults; ?> député<? if ($nResults > 1) echo 's'; ?></p>
<? else : ?>
<p>Groupe parlementaire <? echo $orga->getNom(); ?> : <? echo $nResults; ?> député<? if ($nResults > 1) echo 's'; ?></p>
<? endif; endif; ?>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li>
<? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <a href="<?php echo url_for('parlementaire/list') ?>?slug=<? echo $orga->getSlug(); ?>&page=1">
   << 
    </a>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?slug=<? echo $orga->getSlug(); ?>&page=<?php echo $pager->getPreviousPage() ?>">
   <
    </a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="<?php echo url_for('parlementaire/list') ?>?slug=<? echo $orga->getSlug(); ?>&page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?slug=<? echo $orga->getSlug(); ?>&page=<?php echo $pager->getNextPage() ?>">
   >
    </a>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?slug=<? echo $orga->getSlug(); ?>&page=<?php echo $pager->getLastPage() ?>">
   >>
    </a>
</div>
<? endif; ?>