<?php
  $n_auteurs = count($deputes);
  $line = floor($n_auteurs/(floor($n_auteurs/16)+1));
  $ct = 0;
  foreach ($deputes as $depute) {
    $titre = $depute['nom'].', '.$depute['groupe_acronyme'];
    if ($ct != 0 && $ct != $n_auteurs-1 && !($ct % $line)) echo '<br/>'; $ct++;
    echo '<a href="'.url_for('@parlementaire?slug='.$depute['slug']).'"><img width="50" height="64" title="'.$titre.'" alt="'.$titre.'" src="'.url_for('@resized_photo_parlementaire?height=70&slug='.$depute['slug']).'" /></a>&nbsp;';
  }
?>

