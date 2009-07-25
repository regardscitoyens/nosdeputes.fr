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
<li><? echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <? echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<? echo $parlementaire->getStatut(); ?><? if ($orga->type != 'groupe') : echo ' '.link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); endif; ?>, <? echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?nom_circo='.$parlementaire->nom_circo); ?>)</li>
<? endforeach ; ?>
</ul>
<? if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <? echo link_to('<<', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page=1'); ?>
    <? echo link_to('<', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <? echo link_to('>', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getNextPage()); ?>
    <? echo link_to('>>', '@list_parlementaires_organisme?slug='.$orga->getSlug().'&page='.$pager->getLastPage()); ?>
</div>
<? endif; ?>