<?php

class fixTypeQuestionsTask extends sfBaseTask {

  protected function configure() {
    $this->namespace = 'fix';
    $this->name = 'TypeQuestions';
    $this->briefDescription = 'Set à question au lieu de loi le type des interventions appartenant a une section de questions';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $intervs = Doctrine::getTable('Intervention')->createQuery('i')
      ->leftJoin('i.Section s')
      ->where('s.titre_complet LIKE ?', "question%")
      ->andWhere('i.type = ?', "loi")
      ->orderBy('i.seance_id')
      ->execute();
    echo count($intervs)."\n";

    $seance = -1;
    foreach ($intervs as $itv) {
      if ($seance != $itv->seance_id) {
        $seance = $itv->seance_id;
        echo "Séance N°$seance : http://www.nosdeputes.fr/seance/$seance\n";
      }
      if (preg_match('/^question/i', $itv->Section->titre_complet) && $itv->type != "question") {
        $itv->type = "question";
        $itv->save();
      }
    }
  }

}

