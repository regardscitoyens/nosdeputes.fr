<?php

class setCommissionsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'Commissions';
    $this->briefDescription = 'print correspondances commissions';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('commissions');

 //   $commissions = array();


//    $option->setValue(serialize($commissions));
//    $option->save();
    print_r(unserialize($option->getValue()));
  }
}

