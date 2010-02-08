<div class="amendements">
<?php $nResults = $pager->getNbResults();
  if ($nResults == 0) echo 'Aucun'; else echo $nResults; ?> amendement<?php if ($nResults > 1) echo 's'; ?> trouvé<?php if ($nResults > 1) echo 's';
  if (isset($_GET['search'])) {
    $mots = trim($_GET['search']);
    if ($mots != "") { ?>
<p> pour la recherche sur <em>"<?php echo strip_tags($mots); ?>"</em></p>
<?php } } 
else if (isset($loi)) {
  echo ' sur '.link_to(preg_replace('/Simplifions la loi 2\.0 : (.*)\s*<br.*$/', '\1', $loi->titre), '@loi?loi='.$loi->texteloi_id).' ('.myTools::getLiasseLoiAN($loi).', '.myTools::getLiasseLoiImpr($loi->texteloi_id).')';
} else if (isset($lois)) {
  echo ' portant sur ';
  if (count($lois) > 1) echo 'les projets de loi ';
  else
    echo 'le projet de loi ';
  echo 'N° ';
foreach ($lois as $loi) echo myTools::getLinkLoi($loi).' ('.myTools::getLiasseLoiAN($loi).') '; } ?>
</div>
<?php if ($pager->haveToPaginate()) {
  $uri = $sf_request->getUri();
  $uri = preg_replace('/page=\d+\&?/', '', $uri);
  if (!preg_match('/[\&\?]$/', $uri)) {
    if (preg_match('/\?/', $uri)) {
      $uri .= '&';
    } else{
      $uri .= '?';
    }
  }
  echo '<br/>';
  include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri));
} ?>
<div class="interventions">
<?php foreach($pager->getResults() as $i) {
  $args = array('amendement' => $i);
  if (isset($highlight))
    $args['highlight'] = $highlight;
  echo include_component('amendement', 'parlementaireAmendement', $args);
  }
?></div>
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri)); ?>
