<div class="interventions">
<?php
  $nResults = $pager->getNbResults();
  if ($nResults == 0) $nResults = 'Aucune';
  if (isset($_GET['search'])) {
    $mots = trim($_GET['search']);
    if ($mots != "") { ?>
<p><?php echo $nResults; ?> intervention<?php if ($nResults > 1) echo 's'; ?> trouvée<?php if ($nResults > 1) echo 's'; ?> pour la recherche sur <em>"<?php echo strip_tags($mots); ?>"</em></p>
<?php } } else { ?>
<p><?php echo $nResults; ?> intervention<?php if ($nResults > 1) echo 's'; ?> trouvée<?php if ($nResults > 1) echo 's'; ?>.</p>
<?php } ?>
</div>
<div class="interventions">
<?php foreach($pager->getResults() as $i) {
  $args = array('intervention' => $i);
  if (isset($highlight))
    $args['highlight'] = $highlight;
  if (isset($nophoto))
    $args['nophoto'] = true;
  echo include_component('intervention', 'parlementaireIntervention', $args);
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
include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri));
endif;
