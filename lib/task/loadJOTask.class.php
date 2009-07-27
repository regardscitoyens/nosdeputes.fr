<?php

class loadJOTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'JO';
    $this->briefDescription = 'Load JO data';
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/jo/xml/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
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
	    $commission = Doctrine::getTable('Organisme')->findOneByNomOrCreateIt(strtolower($jo->commission), 'parlementaire');
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
	    $seance->addPresence($depute, 'jo', $jo->source);
	    $seance->free();
	    $commission->free();
	    $depute->free();
	  }
	}
        closedir($dh);
      }
    }
  }
}