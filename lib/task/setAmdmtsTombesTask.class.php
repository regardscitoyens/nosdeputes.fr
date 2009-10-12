<?php

class setAmdmtsTombesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'AmdmtsTombes';
    $this->briefDescription = 'Set Amendements Tombés pour lois zappées';
    $this->addArgument('numero_loi', sfCommandArgument::REQUIRED, 'Numéro de loi zappée'); 
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $amdmts = Doctrine::getTable('Amendement')->findByTexteloiId($arguments['numero_loi']);
    if ($amdmts) foreach($amdmts as $amdmt) {
      $amdmt->sort = 'Tombe';
      $amdmt->save();
      $amdmt->free();
    }
  }
}

