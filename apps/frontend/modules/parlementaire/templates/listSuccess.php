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
    foreach($listlettres as $i) {
      echo '<div class="list_choix" id="'.$i.'">';
      foreach($listlettres as $l) {
        if ($l != $i) echo link_to($l , '@list_parlementaires#'.$l);
        else echo '<big><strong>'.$l.'</strong></big>';
        echo '&nbsp;&nbsp;';
      }
      echo '</div><div class="list_table">';
      include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1));
      echo '</div><div class="suivant"><a href="#">Haut de page</a></div>';
    }
  } else {
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires, 'list' => 1));
    echo '</div>';
  }
} ?>
</div>

