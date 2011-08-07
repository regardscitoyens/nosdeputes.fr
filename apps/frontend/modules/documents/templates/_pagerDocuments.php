<?php use_helper('Text') ?>
<?php $results = $pager->getNbResults();
if ($results == 0) $results = 'Aucun'.$feminin;
$results .= ' '.$typetitre;
if ($results > 1) {
  if ($typetitre === "rapport")
    $results .= 's';
  else $results = str_replace('proposition', 'propositions', $results);
}
$results .= " signé".$feminin;
if ($results > 1)
  $results .= 's';
echo '<p>'.$results.'</p>';
?>
<?php if ($pager->haveToPaginate()) {
 $uri = $sf_request->getUri();
  $uri = preg_replace('/page=\d+\&?/', '', $uri);
  if (!preg_match('/[\&\?]$/', $uri)) {
    if (preg_match('/\?/', $uri))
      $uri .= '&';
    else
      $uri .= '?';
  }
  include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri));
} ?>
<?php foreach($pager->getResults() as $d) {
  echo '<div class="documentlist" id="document'.$d->id.'">';
  echo '<h3>'.link_to(myTools::displayShortDate($d->date)."&nbsp;&mdash; ".$d->getTitre(), '@document?id='.$d->id)."</h3>";
  echo '<a class="contexte" href="'.url_for('@document?id='.$d->id).'">';
 //$d->fonction
echo $d->fonction;
  if ($d->nb_commentaires)
    echo ' (<span class="list_com">'.$d->nb_commentaires.' commentaire';
  if ($d->nb_commentaires > 1)
    echo 's';
  if ($d->nb_commentaires)
    echo ')</span>';
  echo '</a></div>';
  }
?>
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri)); ?>
