<?php

class loadTagCommissionFromDossierTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'tagCommissionFromDossier';
    $this->briefDescription = 'Load tag commission from dossiers';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $dir = dirname(__FILE__).'/../../batch/dossiers/out/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          foreach(file($dir.$file) as $line) {
	    if (preg_match('/([^;]+);(\d+)/', $line, $matches)) {
	      print $matches[1].' => '.$matches[2]."\n";
	      foreach ( Doctrine::getTable('Intervention')->findBySource($matches[1]) as $i) {
		$i->addTag('loi:numero='.$matches[2]);
		$i->save();
		echo "saved\n";
	      }
	    }
	  }
	}
      }
    }
  }
}
