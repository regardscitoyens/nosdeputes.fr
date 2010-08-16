<?php if (!preg_match('/^([A-ZÉ]|all)$/', $search)) { ?>
  <h1>Recherche de députés</h1>
  <?php $sf_response->setTitle('Recherche de députés "'.$search.'"'); ?>
  <p><?php $nResults = count($parlementaires); echo $nResults; ?> parlementaire<?php if ($nResults > 1) echo 's'; ?> trouvé<?php if ($nResults > 1) echo 's'; ?> pour <em>"<?php echo $search; ?>"</em></p>
<?php } else { ?>
  <h1>La liste de tous les députés par ordre alphabétique</h1>
  <?php $sf_response->setTitle('La liste de tous les députés'); ?>
  <p>Les <?php echo $total; ?> députés de la législature (<?php echo $actifs; ?> en cours de mandat)&nbsp;:</p>
<?php } ?>

<div class="liste">
<?php if (isset($similars) && $similars) {
  echo '<p>Peut être, cherchiez vous : </p><ul>';
  foreach($similars as $s) {
    echo '<li>'.link_to($s['nom'], 'parlementaire/show?slug='.$s['slug']).'</li>'; 
  }
  echo '</ul>';  
} else {
  if ($search === "all") {
    $listlettres = array_keys($parlementaires);
    foreach($listlettres as $i) 
      include_partial('parlementaire/table', array('lettre' => $i, 'deputes' => $parlementaires[$i], 'listlettres' => $listlettres));
  } else
    include_partial('parlementaire/table', array('deputes' => $parlementaires));
} ?>
</div>

