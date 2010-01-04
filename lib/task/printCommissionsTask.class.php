<?php

class printCommissionsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'Commissions';
    $this->briefDescription = 'print correspondances commissions';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $option = Doctrine::getTable('VariableGlobale')->findOneByChamp('commissions');
    if ($option) foreach (unserialize($option->getValue()) as $bad => $good)
      echo '"'.$bad.'" => "'.$good.'",'."\n";
  }
}

