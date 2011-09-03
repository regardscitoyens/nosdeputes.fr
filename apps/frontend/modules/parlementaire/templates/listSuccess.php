<h1>Tous les sénateurs par ordre alphabétique</h1> 
<?php $sf_response->setTitle('Liste de tous les sénateurs à l\'Assemblée nationale - NosSénateurs.fr'); ?> 
<p>Les <?php echo $total; ?> sénateurs élus ou ayant exercé un mandat depuis 2004 (<?php echo $actifs; ?> en cours de mandat)&nbsp;:</p> 
<div class="liste"><?php 
$listlettres = array_keys($parlementaires);
foreach($listlettres as $i) {
  echo '<div class="list_choix" id="'.$i.'">';
  foreach($listlettres as $l) {
    if ($l != $i) echo link_to($l , '@list_parlementaires#'.$l);
    else echo '<big><strong>'.$l.'</strong></big>';
    echo '&nbsp;&nbsp;';
  }
  echo '</div><div class="list_table">';
  include_partial('parlementaire/table', array('senateurs' => $parlementaires[$i], 'list' => 1, 'lettre' => $i));
  echo '</div><div class="suivant"><a href="#">Haut de page</a></div>';
}

 ?>
</div>

