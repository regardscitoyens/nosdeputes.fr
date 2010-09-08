<?php

class setMaxDateDossiersTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'MaxDateDossiers';
    $this->briefDescription = 'Set last date à chaque dossier à partir des interventions';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $sections = Doctrine::getTable('Section')->createQuery('s')
      ->where('s.max_date IS NULL')
      ->execute();
    print count($sections)." sections trouvées\n";
    if ($sections) foreach($sections as $section) {
      $query = Doctrine_Query::create()
        ->select('max(i.date) as m')
        ->from('Intervention i')
        ->leftJoin('i.Section s')
        ->where('s.id = ? OR s.section_id = ?', array($section->id, $section->id))
        ->fetchOne();
      $section->setMaxDate($query['m']);
      $section->save();
    }
  }
}

