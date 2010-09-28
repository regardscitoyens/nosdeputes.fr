<?php 

echo '<img title="'.$parlementaire->nom.'<br/>(Groupe parlementaire : '.$parlementaire->groupe_acronyme.')" src="'.url_for('@resized_photo_parlementaire?height='.$height.'&slug='.$parlementaire->slug).'" class="jstitle photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>';
