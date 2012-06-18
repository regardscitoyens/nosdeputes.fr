<?php

class setVacancesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'Vacances';
    $this->briefDescription = 'Load Vacances from Seances';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $semaines = array();

    $manager = new sfDatabaseManager($this->configuration);
    $q = Doctrine_Query::create()->select('s.annee, s.numero_semaine')
      ->from('Seance s')
      ->groupBy('s.annee, s.numero_semaine')
      ->orderBy('s.date ASC');
    $seances = $q->fetchArray();
   if ($seances) {
    $annee = $seances[0]['annee'];
    $sem = $seances[0]['numero_semaine'];
    foreach ($seances as $seance) {
      while (($annee < $seance['annee']) || ($annee == $seance['annee'] && $sem < $seance['numero_semaine'])) {
        array_push($semaines, array("annee" => $annee, "semaine" => $sem));
        if ($sem >= 53) { $annee++; $sem = 1; }
        else $sem++;
      }
      if ($seance['numero_semaine'] >= 53) { $annee = $seance['annee'] + 1 ; $sem = 1; }
      else { $annee = $seance['annee']; $sem = $seance['numero_semaine'] + 1; }
    }
   } else {
    $annee = date('Y', myTools::getDebutLegislature());
    $sem = date('W', myTools::getDebutLegislature());
   }

    $date = time();
    $last_annee = date('Y', $date);
    $last_sem = date('W', $date);
    $day = date('w', $date);
    if ($day < 3) $last_sem--;
    if ($last_sem > 53) { $last_annee++; $last_sem = 1; }
    if ($last_sem == 0) { $last_annee--; $last_sem = 53; }
    while (($annee < $last_annee) || ($annee == $last_annee && $sem <= $last_sem)) {
      array_push($semaines, array("annee" => $annee, "semaine" => $sem));
      if ($sem >= 53) { $annee++; $sem = 1; }
      else $sem++;
    }

    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('vacances');
    if (!$option) {
      $option = new VariableGlobale();
      $option->setChamp('vacances');
      $option->setValue(serialize($semaines));
    } else $option->setValue(serialize($semaines));
    $option->save();
    $option->free();
  }
}

?>
