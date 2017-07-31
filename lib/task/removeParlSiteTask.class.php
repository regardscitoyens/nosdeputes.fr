<?php

class removeParlSiteTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'ParlSite';
    $this->briefDescription = "Supprime un site web d'un député";
    $this->addArgument('parl', sfCommandArgument::REQUIRED, 'id ou nom du parlementaire');
    $this->addArgument('site', sfCommandArgument::REQUIRED, '');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdOrName($arguments['parl']);
    if (!$parl) {
      print "Cannot find parl ".$arguments['parl']."\n";
      return;
    }
    print "FOUND parl : ".$parl->nom."\n";
    $site = $arguments['site'];
    $sites = array();
    if ($parl->sites_web) {
      foreach (unserialize($parl->sites_web) as $s) {
        if ($site === $s)
          print "REMOVING $s from websites\n";
        else $sites[] = $s;
      }
    }
    print_r($sites);
    $parl->sites_web = $sites;
    $parl->save();
  }
}

