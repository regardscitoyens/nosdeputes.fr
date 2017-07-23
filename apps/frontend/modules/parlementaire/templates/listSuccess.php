<h1>Tous les députés par ordre alphabétique</h1>
<?php $sf_response->setTitle('Liste de tous les députés à l\'Assemblée nationale - NosDéputés.fr'); ?>
<p>Voici <?php echo $total; ?> députés de la <?php echo sfConfig::get('app_legislature', 13); ?><sup>ème</sup> législature (<?php echo $actifs; ?> en cours de mandat). Les informations relatives aux députés des deux législatures précédentes restent accessibles. Cliquez sur les liens suivants pour accéder aux députés <a href="https://2007-2012.nosdeputes.fr/deputes">élus entre 2007 et 2012</a>, et ceux <a href="https://2012-2017.nosdeputes.fr/deputes">élus entre 2012 et 2017</a>.</p>
<center><h4><?php foreach (myTools::getCurrentGroupesInfos() as $gpe)
  echo '&nbsp; '.link_to(str_replace(' ', '&nbsp;', $gpe[0].' (<b').' class="c_'.strtolower($gpe[1]).'">'.$gpe[1].'</b>)', '@list_parlementaires_groupe?acro='.$gpe[1]).'&nbsp; '; ?>
</h4></center>

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
