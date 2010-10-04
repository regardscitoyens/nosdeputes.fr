<?php 
if (isset($flip) && $flip)
  $add = '&flip='.$flip;
if ($parlementaire->slug)
echo '<img title="'.$parlementaire->nom.' -- (Groupe parlementaire : '.$parlementaire->groupe_acronyme.')" src="'.url_for('@resized_photo_parlementaire?height='.$height.'&slug='.$parlementaire->slug.$add).'" class="jstitle photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>';
