<?php function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div class="travaux_parlementaires">
<?php $date0 = $seances[0]->date; ?>
<h1>Séances de la semaine du <?php echo myTools::displayDate($date0); ?></h1>
<?php $start = 1; $type0 = $seances[0]->getTypeOrga();
foreach ($seances as $seance) {
  $date = $seance->date;
  $type = $seance->getTypeOrga();
  if ($start) {
    echo '<ul><li><h2>'.myTools::displayDateSemaine($date).'</h2><ul>';
    echo '<li><h3>'.$type.'</h3><ul>';
    $start = 0;
  }
  if ($date != $date0) {
    $date0 = $date;
    echo '</ul></li></ul></li><li><h2>'.myTools::displayDateSemaine($date).'</h2><ul>';
    echo '<li><h3>'.$type.'</h3><ul>';
    $type0 = $type;
  }
  if ($type != $type0) {
    $type0 = $type;
    echo '</ul></li><li><h3>'.$type.'</h3><ul>';
  }
  echo '<li><a href="'.link_tof('interventions_seance', array('seance' => $seance->id)).'">'.preg_replace('/:/', 'H', $seance->getMoment())."</a>";
  if ($type == "Hémicycle") {
    echo '<ul>';
    foreach ($seance->getTableMatiere() as $section) {
      if ($section['section_id'] == $section['id'] && !(preg_match('/(ordre\sdu\sjour|suspension\sde\séance)/i', $section['titre'])))
        echo '<li><a href="'.link_tof('interventions_seance', array('seance' => $seance->id)).'#table_'.$section['id'].'">'.$section['titre'].'</a></li>';
    }
    echo '</ul>';
  }
  echo '</li>';
} ?>
</ul></li></ul></div>
