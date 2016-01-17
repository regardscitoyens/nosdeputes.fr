<?php

class addTagLoiInterventionTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'add';
    $this->name = 'TagLoiIntervention';
    $this->briefDescription = "Ajoute le tag d'une loi à toutes les interventions d'une séance";
    $this->addArgument('loi', sfCommandArgument::REQUIRED, 'numéro de loi');
    $this->addArgument('intervention', sfCommandArgument::REQUIRED, "id de l'intervention");
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $intervention = Doctrine::getTable('Intervention')->findOneById($arguments['intervention']);
    if ($intervention) {
      $intervention->addTag('loi:numero='.$arguments['loi']);
      $intervention->save();
    }
  }
}

