<?php

class printCircosSolrTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'CircosSolr';
    $this->briefDescription = 'print url objets circonscriptions pour Solr';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $ct = 1;
    foreach (Parlementaire::$dptmt_pref as $dep => $pref) {
      echo "url$ct:\n";
      echo "  url: \"/circonscription/departement/".str_replace(' ', '_', $dep)."\"\n";
      echo "  title: \"Les députés ".$pref.(!preg_match("/'/", $pref) ? ' ' : '').$dep.' ('.Parlementaire::getNumeroDepartement($dep).')"'."\n";
      echo "  weight: 10\n";
      $ct++;
    }
  }
}

