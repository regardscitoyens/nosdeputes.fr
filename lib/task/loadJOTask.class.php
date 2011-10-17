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
	$correspondanceheureseance = array();
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
	    if (!$jo->senateur) {
	      echo "ERROR senateur : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $senateur = Doctrine::getTable('Parlementaire')->findOneByNom($jo->senateur);
	    if ($jo->senateur && !$senateur) {
	      $senateur = Doctrine::getTable('Parlementaire')->similarTo($jo->senateur);
	    }
	    if (!$senateur) {
	      echo "ERROR senateur : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    if (!$jo->commission) {
	      echo "ERROR commission : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $commission = Doctrine::getTable('Organisme')->findOneByNomOrCreateIt($jo->commission, 'parlementaire');
	    if (!isset($jo->date) || !$jo->date || $jo->date < 1990) {
	      $senateur->clearRelated();
	      $commission->clearRelated();
	      echo "ERROR date : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    if (!isset($jo->heure) || !$jo->heure)
	      $jo->heure = '1ere séance';
	    //Pour sénat si heure alors 1ère/2d/3ième séance
	    if(preg_match('/(\d)\:\d/', $jo->heure, $match)) {
	      if (isset($correspondanceheureseance[$match[1]])) {
		$jo->heure = $correspondanceheureseance[$match[1]];
	      }else{
		$jo->heure = "1ère séance";
		if ($match[1] > 14) {
		  $count = Doctrine::getTable('Seance')->createQuery('s')
		    ->where('organisme_id = ?', $commission->id)
		    ->andWhere('date = ?', $jo->date)->count();
		  if ($count > 1) {
		    if ($match[1] < 20) {
		      $jo->heure = "2ième séance";
		    }else{
		      $jo->heure = $count."ième séance";
		    }
		  }
		}
		$correspondanceheureseance[$match[1]] = $jo->heure;	      
	      }
	    }
	    $seance = $commission->getSeanceByDateAndMomentOrCreateIt($jo->date, $jo->heure);
	    $seance->addPresence($senateur, $typesource, $jo->source);
	  }
	  unlink($dir.$file);
	}
        closedir($dh);
      }
    }
  }
}
