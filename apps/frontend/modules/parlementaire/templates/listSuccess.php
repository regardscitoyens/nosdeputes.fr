<h1>Tous les députés par ordre alphabétique</h1>
<p>Retrouvez ici l'ensemble des <?php echo $total; ?> députés de la <?php echo sfConfig::get('app_legislature', 13); ?><sup>ème</sup> législature<?php if ($actifs != $total) echo " (dont ".$actifs." en cours de mandat)"; ?>.<br/>
Les informations relatives aux députés des précédentes législatures restent accessibles sur les liens suivants&nbsp;: <a style="text-decoration:underline" href="https://2007-2012.nosdeputes.fr/deputes">entre 2007 et 2012</a>, <a style="text-decoration:underline" href="https://2012-2017.nosdeputes.fr/deputes">entre 2012 et 2017</a>, et <a style="text-decoration:underline" href="https://2017-2022.nosdeputes.fr/deputes">entre 2017 et 2022</a>.</p>
<center>
  <div class="plot_groupes">
    <h4>
    <?php foreach (myTools::getCurrentGroupesInfos() as $gpe)
      echo '<span title="'.$gpe[3].'" class="jstitle"><span class="square c_b_'.strtolower($gpe[1]).'"></span>'.link_to(str_replace(' ', '&nbsp;', $gpe[0].' (<b').' class="c_'.strtolower($gpe[1]).'">'.$gpe[1].'</b>)', '@list_parlementaires_groupe?acro='.$gpe[1]).'</span>&nbsp;&nbsp;&nbsp;&nbsp; '; ?>
    </h4>
    <?php echo include_component('plot', 'groupes', array('plot' => 'groupes', 'groupes' => $groupes, 'nolegend' => true)); ?>
  </div>
</center>

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
