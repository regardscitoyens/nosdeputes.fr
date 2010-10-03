<?php

class printCircosSolrTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'print';
    $this->name = 'CircosSolr';
    $this->briefDescription = 'print url objets circonscriptions pour Solr';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    foreach (Parlementaire::$dptmt_pref as $dep => $pref) {
      $num = Parlementaire::getNumeroDepartement($dep);
      if (preg_match('/^\d$/', $num))
        $num = sprintf("%02d",$num);
      if (preg_match('/\d[a-z]/i', $num))
        $fixednum = '0'.strtolower($num);
      else 
	$fixednum = sprintf('%03d',$num);
      echo "circo$num:\n";
      echo "  url: \"/circonscription/departement/".str_replace(' ', '_', $dep)."\"\n";
      echo "  title: \"Les députés ".$pref.(!preg_match("/'/", $pref) ? ' ' : '').$dep.' ('.Parlementaire::getNumeroDepartement($dep).')"'."\n";
      echo "  image: \"<img width='53' class='jstitle' title='".$dep." (".$num.")' alt='".$dep." (".$num.")' src='/circonscription/image/".$fixednum."/53/0'/>\"\n";
      echo "  weight: 10\n";
    }
  }
}

