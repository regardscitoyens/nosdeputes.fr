<?php 
$add='';
if (isset($flip) && $flip)
  $add = '&flip='.$flip;
if ($parlementaire->fin_mandat != null && $parlementaire->fin_mandat >= $parlementaire->debut_mandat)
  $groupe = "ancien".($parlementaire->sexe == "F" ? "ne" : "")." sénat".($parlementaire->sexe == "F" ? "rice" : "eur");
else $groupe = "Groupe parlementaire : ".$parlementaire->groupe_acronyme;
if ($parlementaire->slug)
echo '<img title="'.$parlementaire->nom.' -- ('.$groupe.')" src="'.url_for('@resized_photo_parlementaire?height='.$height.'&slug='.$parlementaire->slug.$add).'" class="jstitle photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>';
?>
