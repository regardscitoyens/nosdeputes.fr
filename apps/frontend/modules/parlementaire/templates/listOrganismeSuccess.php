<?php $nResults = $pager->getNbResults(); ?>
<h1><?php echo $orga->getNom(); $sf_response->setTitle($orga->getNom()); ?></h1>
<?php include_component('article', 'show', array('categorie'=>'Organisme', 'object_id'=>$orga->id)); ?>
<?php if ($nResults) {if ($orga->type == 'extra') : ?>
<h2>Organisme extra-parlementaire composé de <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?>&nbsp;:</h2>
<?php else : ?>
<h2><?php if (preg_match('/commission/i', $orga->getNom())) echo 'Comm'; else echo 'M'; ?>ission parlementaire composée de <?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?>&nbsp;:</h2>
<?php endif; }?>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <?php
echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php
echo $parlementaire->getStatut(1).", ".link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li>
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
<?php endif;
if (count($seances)) { ?>
<div><h3>Les dernières réunions de la <?php if (preg_match('/commission/i', $orga->getNom())) echo 'Comm'; else echo 'M'; ?>ission</h3>
<ul>
<?php $cpt = 0; foreach($seances as $seance) { $cpt++;?>
<li><?php echo link_to($seance->getTitre(), '@interventions_seance?seance='.$seance->id); ?></li>
<?php if ($cpt > 10) break;} ?>
</ul>
</div>
<?php } ?>
