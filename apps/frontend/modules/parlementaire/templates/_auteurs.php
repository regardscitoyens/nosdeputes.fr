<?php
  $total = count($senateurs);
  for ($ct=0;$ct<$total;$ct++) {
    echo link_to($senateurs[$ct]['nom'], '@parlementaire?slug='.$senateurs[$ct]['slug']);
    if ($senateurs[$ct]['ParlementaireTexteloi'][0]['fonction']) {
      $fonction = preg_replace('/(auteur|cosignataire)\s*/', '', strtolower($senateurs[$ct]['ParlementaireTexteloi'][0]['fonction']));
      if (isset($orga) && $orga->id)
        $fonction = preg_replace('/commission.*$/i', link_to($orga->nom, '@list_parlementaires_organisme?slug='.$orga->slug), $fonction);
      if ($fonction) echo " ".$fonction;
    }
    if ((isset($orga) && $orga->id) || $ct == $total - 1) echo '<br/>';
    else if ($ct == $total - 2) echo " &&nbsp;";
    else echo ", ";
  } 
?>

