<?php function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div class="travaux_parlementaires">
<?php $date0 = 0; ?>
<h1>Séances de la semaine du <?php echo myTools::displayDate($date0); ?></h1>
<?php if ($seances) {
  $date0 = $seances[0]->date;
  $type0 = $seances[0]->getTypeOrga();
  echo '<ul><li><h2>'.myTools::displayDateSemaine($date0).'</h2><ul>';
  echo '<li><h3>'.$type0.'</h3><ul>';
  foreach ($seances as $seance) {
    $date = $seance->date;
    $type = $seance->getTypeOrga();
    if ($date0 != $date) {
      $date0 = $date;
      $type0 = $type;
      echo '</ul></li></ul></li><li><h2>'.myTools::displayDateSemaine($date).'</h2><ul>';
      echo '<li><h3>'.$type.'</h3><ul>';
    } else if ($type0 != $type) {
      $type0 = $type;
      echo '</ul></li><li><h3>'.$type.'</h3><ul>';
    }
    echo '<li><a href="'.link_tof('interventions_seance', array('seance' => $seance->id)).'">'.preg_replace('/:/', 'H', $seance->getMoment())."</a>";
    if ($type == "Hémicycle") {
      echo '<ul>';
      foreach ($seance->getTableMatiere() as $section) {
        if ($section['section_id'] == $section['id'] && $section['id'] != 1 && !(preg_match('/(ordre\sdu\sjour|suspension\sde\séance)/i', $section['titre'])))
          echo '<li><a href="'.link_tof('interventions_seance', array('seance' => $seance->id)).'#table_'.$section['id'].'">'.$section['titre'].'</a></li>';
      }
      echo '</ul>';
    }
    echo '</li>';
  } ?>
  </ul></li>
  </ul></li>
  </ul>
<?php } ?></div>
