<h1>Tous les députés par ordre alphabétique</h1> 
<?php $sf_response->setTitle('Liste de tous les députés à l\'Assemblée nationale - NosDéputés.fr'); ?> 
<p>Les <?php echo $total; ?> députés de la <?php echo sfConfig::get('app_legislature', 13); ?><sup>ème</sup> législature (<?php echo $actifs; ?> en cours de mandat)&nbsp;:</p> 
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
  include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'lettre' => $i));
  echo '</div><div class="suivant"><a href="#">Haut de page</a></div>';
}

 ?>
</div>

