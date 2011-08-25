<?php
  $n_auteurs = count($senateurs);
  $line = floor($n_auteurs/(floor($n_auteurs/15)+1));
  $ct = 0;
  foreach ($senateurs as $senateur) {
    $titre = $senateur['nom'].', '.$senateur['groupe_acronyme'];
    if ($ct != 0 && $ct != $n_auteurs-1 && !($ct % $line)) echo '<br/>'; $ct++;
    echo '<a href="'.url_for('@parlementaire?slug='.$senateur['slug']).'">';
    include_partial('parlementaire/photoParlementaire', array('parlementaire' => $senateur, 'height' => 70));
    echo '</a>&nbsp;';
  }
?>

