<?php

class addTagLoiSeanceTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'add';
    $this->name = 'TagLoiSeance';
    $this->briefDescription = 'Corrige le tagging pour un numéro de loi mal enregistré de la forme xxxxyyyy';
    $this->addArgument('loi', sfCommandArgument::REQUIRED, 'numéro de loi');
    $this->addArgument('seance', sfCommandArgument::REQUIRED, 'id de la séance');
    $this->addArgument('dossier', sfCommandArgument::REQUIRED, 'id du dossier a tagger');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $interventions = Doctrine::getTable('Intervention')->findBySeanceId($arguments['seance']);
    if ($interventions) foreach($interventions as $i) {
      if ($i->section_id == $arguments['dossier'] || $i->Section->section_id == $arguments['dossier']) {
        $i->addTagLois(array($arguments['loi']));
        $i->save();
      }
    }
  }
}

