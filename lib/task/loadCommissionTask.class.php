<?php

class loadCommissionTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Commission';
    $this->briefDescription = 'Load Commission data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/commission/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  if (preg_match('/^\./', $file))
	    continue;
	  echo "$dir$file\n";
          $first = 1;
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!$json || !$json->intervention || !$json->date || !$json->commission || !$json->source) {
	      echo "ERROR json : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }

	    if (strlen($json->commission) > 255) {
	      $json->commission = preg_replace('/ \S+$/', '', substr($json->commission, 0, 255));
	    }

	    if ($first) { #On teste si la séance existe déjà.
		$first = 0;
		$seance = Doctrine::getTable('Seance')->getFromSeanceArgs('commission', $json->date, $json->heure, $json->session, $json->commission);
		if ($seance) {
		  try {
		    $seance->deleteInterventions();
		  }catch(Exception $e) {
		    echo "ERROR: Impossible de supprimer la séance ".$seance->id." (".$e->getMessage().")\n";
		    continue 2;
		  }
		}
	    }

	    $id = md5($json->intervention.$json->date.$json->heure.$json->commission);
	    $intervention = Doctrine::getTable('Intervention')->findOneByMd5($id);
	    if(!$intervention) {
	      $intervention = new Intervention();
	      $intervention->md5 = $id;
	      $intervention->setIntervention($json->intervention);
	      $intervention->setSeance('commission', $json->date, $json->heure, $json->session, $json->commission);
	      $intervention->date = $json->date;
	      $intervention->setSource($json->source);
	      $intervention->setTimestamp($json->timestamp);
	    }
	    if ($json->intervenant) {
	      $intervention->setPersonnaliteByNom($json->intervenant, $json->fonction);
	    }
	    $intervention->save();
	    $intervention->free();
	  }
	  unlink($dir.$file);
	}
        closedir($dh);
      }
    }
  }
}
