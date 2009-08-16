<div class="temp">
<div class="amendements">
<?php
  if (isset($_GET['search']))
    $mots = trim($_GET['search']);
  else $mots = "";
  $nResults = $pager->getNbResults();
  if ($mots == "") : ?>
<p>Les 5000 derniers amendements en date&nbsp;:</p>
<?php else : ?>
<p><?php echo $nResults; ?> amendement<?php if ($nResults > 1) echo 's'; ?> trouvé<?php if ($nResults > 1) echo 's'; ?> pour la recherche sur <em>"<?php echo $mots; ?>"</em></p>
<?php endif; ?>
</div>
<div class="amendements">
<?php foreach($pager->getResults() as $i) {
  $args = array('amendement' => $i);
  if (isset($highlight))
    $args['highlight'] = $highlight;
  echo include_component('amendement', 'parlementaireAmendement', $args);
  }
?></div>
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
</div>