<?php $nResults = $pager->getNbResults(); ?>
<h1>Composition du groupe "<?php echo $orga->getNom(); $sf_response->setTitle('Composition du groupe "'.$orga->getNom().'"'); ?>" (<?php echo $orga->getSmallNomGroupe(); ?>)</h1><h2><?php echo $nResults; ?> député<?php if ($nResults > 1) echo 's'; ?>&nbsp;:</h2>
<ul>
<?php foreach($pager->getResults() as $parlementaire) : ?>
<li><?php echo $parlementaire->getPOrganisme($orga->getNom())->getFonction(); ?> : <?php echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); ?> (<?php echo link_to($parlementaire->nom_circo, '@list_parlementaires_circo?search='.$parlementaire->nom_circo); ?>)</li>
<?php endforeach ; ?>
</ul>
<?php if ($pager->haveToPaginate()) :

$uri = $sf_request->getUri();
$uri = preg_replace('/page=\d+\&?/', '', $uri);

if (!preg_match('/[\&\?]$/', $uri)) {
  if (preg_match('/\?/', $uri)) {
    $uri .= '&';
  }else{
    $uri .= '?';
  }
}
?>
<div class="pagination">
    <?php echo link_to('<< ', $uri.'page=1'); ?>
    <?php echo link_to('< ', $uri.'page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, $uri.'page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', $uri.'page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', $uri.'page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
