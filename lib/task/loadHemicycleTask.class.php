<?php

class loadHemicyleTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Hemicycle';
    $this->briefDescription = 'Load Hemicycle data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/hemicycle/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  $sections = array();
	  if (preg_match('/^\./', $file))
	    continue;
	  echo "$dir$file\n";
          $debug = 1;
	  $seance = 0;
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!$json || !$json->intervention || !$json->date || !$json->heure || !$json->source) {
	      echo "ERROR json : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
            $date = $json->date;
	    $id = md5($json->intervention.$json->date.$json->heure.'hemicyle'.$json->timestamp);
	    $intervention = Doctrine::getTable('Intervention')->findOneByMd5($id);
	    if ($seance && !$intervention) {
	      $inter = Doctrine::getTable('Intervention')->findOneBySeanceTimestamp($seance, $json->timestamp);
	      if ($inter) {
		$res = similar_text($inter->getIntervention(), $json->intervention, $pc);
	        if ($res > 0 && $pc > 75)
	          $intervention = $inter;
		  $intervention->setIntervention($json->intervention);
		  $intervention->md5 = $id;
		  echo "WARNING : Intervention en double trouvée : seance/".$seance."#inter_".$id."\n"; 
	      }
            }
	    if(!$intervention) {
	      $intervention = new Intervention();
	      $intervention->md5 = $id;
	      $intervention->setIntervention($json->intervention);
	      if (preg_match('/^question/i', $json->contexte))
    		$type = 'question';
	      else
        	$type = 'loi';
	      $intervention->date = $json->date;
	      $intervention->setSeance($type, $json->date, $json->heure, $json->session);
	      $intervention->setSource($json->source);
	      $intervention->setTimestamp($json->timestamp);
	    } else $seance = $intervention->seance_id;
	    $lois = null;
	    if (isset($json->numeros_loi))
	      $lois = $json->numeros_loi;
            if ($json->timestamp)
              $debug = $intervention->setContexte($json->contexte, $json->date.$json->heure, $json->timestamp, $lois, $debug);
	    if (isset($json->amendements))
	      $intervention->setAmendements($json->amendements);
	    if ($json->intervenant) {
	      $p = null;
	      $fonction = null;
	      if (isset($json->fonction))
	        $fonction = $json->fonction;
	      if (isset($json->intervenant_url)) {
                $p = Doctrine::getTable('Parlementaire')
                  ->findOneByUrlAn($json->intervenant_url);
                if ($p) {
                  $intervention->setParlementaire($p);
                  $intervention->setFonction($fonction);
                }
	      }
	      if (!$p) {
                $intervention->setPersonnaliteByNom($json->intervenant, $fonction);
	      } else $p->free();
	    }
	    $intervention->save();
	    if (!isset($sections[$intervention->getSection()->id]))
	      $sections[$intervention->getSection()->id] = $intervention->getSection();
	    $intervention->free();
	  }
	  foreach(array_values($sections) as $section) {
	    $section->updateNbInterventions();
            $section->setMaxDate($date);
          }
          if ($section) {
	    $section->free();
	  }
	  unset($sections);
	  unlink($dir.$file);
	}
        closedir($dh);
      }
    }
  }
}
