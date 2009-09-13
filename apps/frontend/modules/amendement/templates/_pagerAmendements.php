<div class="amendements">
<?php $nResults = $pager->getNbResults();
  if ($nResults == 0) echo 'Aucun'; else echo $nResults; ?> amendement<?php if ($nResults > 1) echo 's'; ?> trouvé<?php if ($nResults > 1) echo 's';
  if (isset($_GET['search'])) {
    $mots = trim($_GET['search']);
    if ($mots != "") { ?>
<p> pour la recherche sur <em>"<?php echo strip_tags($mots); ?>"</em></p>
<?php } } 
else if (isset($lois)) {
  echo ' pour ';
  if (count($lois) > 1) echo 'les projets de loi ';
  else
    echo 'le projet de loi ';
  echo 'N° ';
  foreach ($lois as $loi) echo myTools::getLinkLoi($loi).' ';
} ?>
</div>
<div class="interventions">
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
include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri));
