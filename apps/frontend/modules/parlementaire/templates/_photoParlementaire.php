<?php 
$add='';
$groupe = "";
if (isset($flip) && $flip)
  $add = '&flip='.$flip;
if ($parlementaire->fin_mandat != null && $parlementaire->fin_mandat >= $parlementaire->debut_mandat)
  $groupe = " -- (ancien".($parlementaire->sexe == "F" ? "ne" : "")." député".($parlementaire->sexe == "F" ? "e" : "").')';
else if ($parlementaire->groupe_acronyme) $groupe = " -- (Groupe parlementaire : ".$parlementaire->groupe_acronyme.')';
$abs = '';
if (isset($absolute) && $absolute)
  $abs = 'absolute=true';
if ($parlementaire->slug)
  echo '<img alt="Photo issue du site de l\'Assemblée nationale ou de Wikipedia" title="'.$parlementaire->nom.$groupe.'" src="'.url_for('@resized_photo_parlementaire?height='.$height.'&slug='.$parlementaire->slug.$add, $abs).'" class="jstitle photo_fiche" style="width:'.round($height*156/200).'px; height:'.$height.'px;" alt="Photo de '.$parlementaire->nom.'"/>';
