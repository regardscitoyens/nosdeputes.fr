<?php

class setAmdmtSortTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'AmdmtSort';
    $this->briefDescription = "Définit le sort d'un amendement";
    $this->addArgument('loi', sfCommandArgument::REQUIRED, 'numero loi');
    $this->addArgument('num', sfCommandArgument::REQUIRED, 'numero amdmt');
    $this->addArgument('sort', sfCommandArgument::REQUIRED, 'sort à définir');
    $this->addArgument('apply', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour activer modif réelle');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $config = sfYaml::load(dirname(__FILE__).'/../../config/app.yml');
    $legis = $config['all']['legislature'];
    $amdt = Doctrine::getTable('Amendement')->findLastOneByLegisLoiNum($legis, $arguments['loi'], $arguments['num']);
    if (!$amdt) {
      print "Cannot find amdt \n";
      return;
    }
    print "FOUND amdt : ".$amdt->sort." / ".$amdt->texte."\n";
    if ($arguments['apply']) {
      print "setting new status ".$arguments['sort']."\n";
      $amdt->sort = $arguments['sort'];
      $amdt->save();
    }
    return;
  }
}

