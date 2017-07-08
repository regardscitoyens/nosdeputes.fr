<?php

class setParlTwitterTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'ParlTwitter';
    $this->briefDescription = "Définit le compte twitter d'un parlementaire";
    $this->addArgument('parl', sfCommandArgument::REQUIRED, 'id ou nom du parlementaire');
    $this->addArgument('twitter', sfCommandArgument::REQUIRED, '');
    $this->addArgument('fonction', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour donner une fonction au parlementaire sur les interventions modifiées');
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
    $sites = array();
    if ($parl->sites_web) {
      foreach (unserialize($parl->sites_web) as $s) {
        if (preg_match('/twitter.com\//', $s))
          print "REMOVING $s to its websites\n";
        else $sites[] = $s;
      }
    }
    print_r($sites);
    $twitter = str_replace('@', '', trim($arguments['twitter']));
    $turl = "https://twitter.com/$twitter";
    print "ADDING $turl to its websites\n";
    $sites[] = $turl;
    $parl->sites_web = $sites;
    $parl->save();
  }
}

