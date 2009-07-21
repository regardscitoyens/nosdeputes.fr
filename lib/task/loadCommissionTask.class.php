<?php

class loadCommissionTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Commission';
    $this->briefDescription = 'Load Commission data';
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/commission/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  print "$dir$file\n";
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!$json && !$json->intervention && !$json->date && !$json->heure && !$json->commission && !$json->source) {
	      echo "ERROR json : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $id = md5($json->intervention.$json->date.$json->heure.$json->commission);
	    $intervention = Doctrine::getTable('Intervention')->find($id);
	    if(!$intervention) {
	      $intervention = new Intervention();
	      $intervention->id = $id;
	      $intervention->intervention = $json->intervention;
	      $intervention->setSeance($json->commission, $json->date, $json->heure);
	      $intervention->setSource($json->source);
	      $intervention->timestamp = $json->timestamp;
	    }
	    if ($json->intervenant)
	      $intervention->setPersonnaliteByNom($json->intervenant);
	    $intervention->save();
	  }
	  
	}
        closedir($dh);
      }
    }
  }
}