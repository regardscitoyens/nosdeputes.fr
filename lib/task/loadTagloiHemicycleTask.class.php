<?php

class loadTagLoiHemicyleTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'TagLoiHemicycle';
    $this->briefDescription = 'Load Hemicycle data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/hemicycle/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        $cpt = 0;
        while (($file = readdir($dh)) !== false) {
	  $sections = array();
	  if (preg_match('/^\./', $file))
	    continue;
	  echo "$dir$file\n";
          $debug = 1;
          if (!filesize($dir.$file)) {
		echo "ERROR file empty : $file\n";
		unlink($dir.$file);
		continue;
	  }
          if ($cpt > 9)
                exit(1);
          $cpt ++;
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
            $error = 0;
	    if (!$json)
		$error = "cannot parse json";
	    else if (!$json->intervention)
		$error = "pas d'intervention";
	    else if (!$json->date)
		$error = "pas de date";
	    else if (!$json->heure)
		$error = "pas d'heure";
	    else if (!$json->source) 
		$error = "pas de source";
	    if ($error) {
	      echo "ERROR json ($error): ";
	      echo $line;
	      echo "\n => ";
	      print_r($json);
	      $contraints = get_defined_constants(true);
	      print_r($contraints["json"]);
	      continue;
	    }
            $date = $json->date;
	    $id = md5($json->intervention.$json->date.$json->heure.'hemicyle'.$json->timestamp);
	    $intervention = Doctrine::getTable('Intervention')->findOneByMd5($id);
	    if(!$intervention) {
		continue;
	    }
	    if (!isset($json->numeros_loi))
		continue;
            if ($json->timestamp) {
              $intervention->updateTagLois($json->numeros_loi);
	    }
	    $intervention->save();
	  }
	  unlink($dir.$file);
	}
        closedir($dh);
      }
    }
  }
}
