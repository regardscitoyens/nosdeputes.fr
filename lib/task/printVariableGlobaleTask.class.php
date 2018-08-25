<?php

class printVariableGlobaleTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'VariableGlobale';
    $this->briefDescription = 'print unserialized VariableGlobale';
    $this->addArgument('variable', sfCommandArgument::REQUIRED, 'Champ variable globale');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp($arguments['variable']);
    if ($option) print_r(unserialize($option->getValue()));
  }
}

