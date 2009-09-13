<div class="questions">
<?php
  $nResults = $pager->getNbResults();
  if ($nResults == 0) $nResults = 'Aucune';
  if (isset($_GET['search'])) {
    $mots = trim($_GET['search']);
    if ($mots != "") { ?>
<p><?php echo $nResults; ?> question<?php if ($nResults > 1) echo 's'; ?> trouvée<?php if ($nResults > 1) echo 's'; ?> pour la recherche sur <em>"<?php echo $mots; ?>"</em></p>
<?php } } else { ?>
<p><?php echo $nResults; ?> question<?php if ($nResults > 1) echo 's'; ?> trouvée<?php if ($nResults > 1) echo 's'; ?>.</p>
<?php } ?>
</div>
<div class="questions">
<?php foreach($pager->getResults() as $i) {
  $args = array('question' => $i);
  if (isset($highlight))
    $args['highlight'] = $highlight;
  if (isset($nophoto))
    $args['nophoto'] = true;
  echo include_component('questions', 'search', $args);
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

endif; ?>
