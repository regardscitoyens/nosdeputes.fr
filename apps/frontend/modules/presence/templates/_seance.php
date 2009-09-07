<?php
  foreach ($presences as $presence) {
    $depute = $presence->getParlementaire();
    $titre = $depute->getNom().', '.$depute->groupe_acronyme;
    echo '<a href="'.url_for($depute->getPageLink()).'"><img width="50" height="64" title="'.$titre.'" alt="'.$titre.'" src="'.url_for('@resized_photo_parlementaire?height=70&slug='.$depute->slug).'" /></a>&nbsp;';
  }
?>