<?php
  $n_auteurs = count($deputes);
  $line = floor($n_auteurs/(floor($n_auteurs/15)+1));
  $ct = 0;
  foreach ($deputes as $depute) {
    $titre = $depute['nom'].', '.$depute['groupe_acronyme'];
    if ($ct != 0 && $ct != $n_auteurs-1 && !($ct % $line)) echo '<br/>'; $ct++;
    echo '<a href="'.url_for('@parlementaire?slug='.$depute['slug']).'">';
    include_partial('parlementaire/photoParlementaire', array('parlementaire' => $depute, 'height' => 70));
    echo '</a>&nbsp;';
  }
?>

