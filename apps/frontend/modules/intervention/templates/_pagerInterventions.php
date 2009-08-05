<p><?php $nResults = $pager->getNbResults(); echo $nResults; ?> résultat<?php if ($nResults > 1) echo 's'; ?></p>
<?php foreach($pager->getResults() as $i) {
  $args = array('intervention' => $i);
  if (isset($highlight))
    $args['highlight'] = $highlight;
  echo include_component('intervention', 'parlementaireIntervention', $args);
  }
?></p>