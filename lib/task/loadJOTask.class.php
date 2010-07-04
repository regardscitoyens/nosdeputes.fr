<?php

class loadJOTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'JO';
    $this->briefDescription = 'Load Présences from JO data or CRI';
    $this->addOption('source', null, sfCommandOption::PARAMETER_OPTIONAL, 'Define the source to load: jo or cri', 'jo');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    if ($options['source'] === "jo") {
      $workdir = "jo/xml";
      $typesource = "jo";
    } else if ($options['source'] === "cri") {
      $workdir = "commission/presents";
      $typesource = "compte-rendu";
    } else {
      echo "Error wrong value for option --source, choose cri or jo";
      return;
    }
    $dir = dirname(__FILE__).'/../../batch/'.$workdir.'/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  if (! is_file($dir.$file))
	     continue;
	  foreach(file($dir.$file) as $line) {
	    $jo = json_decode($line);
	    if (!$jo) {
	      echo "ERROR json : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    if (!$jo->depute) {
	      echo "ERROR null : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $depute = Doctrine::getTable('Parlementaire')->findOneByNom($jo->depute);
	    if ($jo->depute && !$depute) {
	      $depute = Doctrine::getTable('Parlementaire')->similarTo($jo->depute);
	    }
	    if (!$depute) {
	      echo "ERROR depute : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $commission = Doctrine::getTable('Organisme')->findOneByNomOrCreateIt($jo->commission, 'parlementaire');
	    if (!$jo->reunion) {
	      $depute->clearRelated();
	      $depute->free();
	      $commission->clearRelated();
	      $commission->free();
	      echo "ERROR date : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $seance = $commission->getSeanceByDateAndMomentOrCreateIt($jo->reunion, $jo->session);
	    $seance->addPresence($depute, $typesource, $jo->source);
	    $seance->free();
	    $commission->free();
	    $depute->free();
	  }
	  unlink($dir.$file);
	}
        closedir($dh);
      }
    }
  }
}
